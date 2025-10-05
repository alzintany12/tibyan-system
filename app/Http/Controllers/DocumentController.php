<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\LegalCase;
use App\Models\DocumentVersion;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DocumentController extends Controller
{
    /**
     * Display a listing of the documents.
     */
    public function index(Request $request)
    {
        $query = Document::with(['legalCase', 'user'])
            ->where('is_active', true)
            ->latest();

        // فلترة حسب القضية
        if ($request->filled('case_id')) {
            $query->where('legal_case_id', $request->case_id);
        }

        // فلترة حسب النوع
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $documents = $query->paginate(15);
        $cases = LegalCase::where('is_active', true)->get();

        return view('documents.index', compact('documents', 'cases'));
    }

    /**
     * Show the form for creating a new document.
     */
    public function create(Request $request)
    {
        $cases = LegalCase::where('is_active', true)->get();
        $templates = DocumentTemplate::where('is_active', true)->get();
        
        $selectedCase = null;
        if ($request->filled('case_id')) {
            $selectedCase = LegalCase::find($request->case_id);
        }

        return view('documents.create', compact('cases', 'templates', 'selectedCase'));
    }

    /**
     * Store a newly created document in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:contract,pleading,correspondence,evidence,other',
            'legal_case_id' => 'required|exists:legal_cases,id',
            'file' => 'required|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:10240',
            'is_confidential' => 'boolean',
            'tags' => 'nullable|string',
            'template_id' => 'nullable|exists:document_templates,id'
        ]);

        $document = new Document($validated);
        $document->user_id = Auth::id();
        $document->is_confidential = $request->boolean('is_confidential');

        // رفع الملف
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $filename, 'public');
            
            $document->file_path = $path;
            $document->file_name = $file->getClientOriginalName();
            $document->file_size = $file->getSize();
            $document->mime_type = $file->getMimeType();
        }

        // معالجة التاجز
        if ($request->filled('tags')) {
            $document->tags = array_map('trim', explode(',', $request->tags));
        }

        $document->save();

        // إنشاء الإصدار الأول
        DocumentVersion::create([
            'document_id' => $document->id,
            'version_number' => '1.0',
            'file_path' => $document->file_path,
            'file_name' => $document->file_name,
            'file_size' => $document->file_size,
            'user_id' => Auth::id(),
            'notes' => 'الإصدار الأول'
        ]);

        return redirect()->route('documents.index')
            ->with('success', 'تم إنشاء المستند بنجاح');
    }

    /**
     * Display the specified document.
     */
    public function show(Document $document)
    {
        $document->load(['legalCase', 'user', 'versions.user']);
        
        return view('documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified document.
     */
    public function edit(Document $document)
    {
        $cases = LegalCase::where('is_active', true)->get();
        $templates = DocumentTemplate::where('is_active', true)->get();

        return view('documents.edit', compact('document', 'cases', 'templates'));
    }

    /**
     * Update the specified document in storage.
     */
    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:contract,pleading,correspondence,evidence,other',
            'legal_case_id' => 'required|exists:legal_cases,id',
            'file' => 'nullable|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:10240',
            'is_confidential' => 'boolean',
            'tags' => 'nullable|string'
        ]);

        $document->fill($validated);
        $document->is_confidential = $request->boolean('is_confidential');

        // رفع ملف جديد إذا تم تحديده
        if ($request->hasFile('file')) {
            // حذف الملف القديم
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $filename, 'public');
            
            $document->file_path = $path;
            $document->file_name = $file->getClientOriginalName();
            $document->file_size = $file->getSize();
            $document->mime_type = $file->getMimeType();

            // إنشاء إصدار جديد
            $latestVersion = $document->versions()->latest()->first();
            $newVersionNumber = $this->generateNextVersion($latestVersion ? $latestVersion->version_number : '1.0');

            DocumentVersion::create([
                'document_id' => $document->id,
                'version_number' => $newVersionNumber,
                'file_path' => $document->file_path,
                'file_name' => $document->file_name,
                'file_size' => $document->file_size,
                'user_id' => Auth::id(),
                'notes' => $request->input('version_notes', 'تحديث الملف')
            ]);
        }

        // معالجة التاجز
        if ($request->filled('tags')) {
            $document->tags = array_map('trim', explode(',', $request->tags));
        } else {
            $document->tags = null;
        }

        $document->save();

        return redirect()->route('documents.show', $document)
            ->with('success', 'تم تحديث المستند بنجاح');
    }

    /**
     * Remove the specified document from storage.
     */
    public function destroy(Document $document)
    {
        // وضع علامة كغير نشط بدلاً من الحذف الفعلي
        $document->update(['is_active' => false]);

        return redirect()->route('documents.index')
            ->with('success', 'تم حذف المستند بنجاح');
    }

    /**
     * Download the document file.
     */
    public function download(Document $document)
    {
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'الملف غير موجود');
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    /**
     * Download a specific version of the document.
     */
    public function downloadVersion(Document $document, DocumentVersion $version)
    {
        if ($version->document_id !== $document->id) {
            abort(403, 'غير مصرح بالوصول');
        }

        if (!$version->file_path || !Storage::disk('public')->exists($version->file_path)) {
            abort(404, 'الملف غير موجود');
        }

        return Storage::disk('public')->download($version->file_path, $version->file_name);
    }

    /**
     * Create a new version of the document.
     */
    public function createVersion(Request $request, Document $document)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:10240',
            'notes' => 'nullable|string|max:500'
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('documents/versions', $filename, 'public');

        $latestVersion = $document->versions()->latest()->first();
        $newVersionNumber = $this->generateNextVersion($latestVersion ? $latestVersion->version_number : '1.0');

        DocumentVersion::create([
            'document_id' => $document->id,
            'version_number' => $newVersionNumber,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'user_id' => Auth::id(),
            'notes' => $request->input('notes', 'إصدار جديد')
        ]);

        // تحديث معلومات المستند الرئيسي
        $document->update([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType()
        ]);

        return redirect()->route('documents.show', $document)
            ->with('success', 'تم إنشاء إصدار جديد بنجاح');
    }

    /**
     * Show document templates.
     */
    public function templates()
    {
        $templates = DocumentTemplate::where('is_active', true)->get();
        
        return view('documents.templates', compact('templates'));
    }

    /**
     * Duplicate a document.
     */
    public function duplicate(Document $document)
    {
        $newDocument = $document->replicate();
        $newDocument->title = $document->title . ' (نسخة)';
        $newDocument->user_id = Auth::id();
        $newDocument->created_at = now();
        $newDocument->updated_at = now();
        
        // نسخ الملف
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
            $newFilename = time() . '_copy_' . Str::slug($document->title) . '.' . $extension;
            $newPath = 'documents/' . $newFilename;
            
            Storage::disk('public')->copy($document->file_path, $newPath);
            $newDocument->file_path = $newPath;
        }
        
        $newDocument->save();

        return redirect()->route('documents.edit', $newDocument)
            ->with('success', 'تم نسخ المستند بنجاح');
    }

    /**
     * Secure download with token.
     */
    public function secureDownload($token)
    {
        // يمكن تنفيذ نظام تحميل آمن بالتوكن هنا
        // مثال بسيط:
        $document = Document::where('download_token', $token)->firstOrFail();
        
        return $this->download($document);
    }

    /**
     * Generate next version number.
     */
    private function generateNextVersion($currentVersion)
    {
        $parts = explode('.', $currentVersion);
        $major = intval($parts[0]);
        $minor = isset($parts[1]) ? intval($parts[1]) : 0;
        
        $minor++;
        
        return "{$major}.{$minor}";
    }
}