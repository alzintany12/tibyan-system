<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentTemplateController extends Controller
{
    /**
     * Display a listing of the document templates.
     */
    public function index(Request $request)
    {
        $query = DocumentTemplate::where('is_active', true)
            ->latest();

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // فلترة حسب النوع
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // فلترة حسب الفئة
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $templates = $query->paginate(15);

        return view('document_templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new document template.
     */
    public function create()
    {
        return view('document_templates.create');
    }

    /**
     * Store a newly created document template in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:contract,pleading,correspondence,evidence,other',
            'category' => 'required|string|in:legal,administrative,financial,evidence,correspondence',
            'content' => 'required|string',
            'variables' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
            'is_system_default' => 'boolean'
        ]);

        $template = new DocumentTemplate($validated);
        $template->created_by = Auth::id();
        $template->is_system_default = $request->boolean('is_system_default');

        // رفع الملف إذا تم تحديده
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . Str::slug($validated['name']) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('document-templates', $filename, 'public');
            
            $template->file_path = $path;
            $template->file_name = $file->getClientOriginalName();
            $template->file_size = $file->getSize();
            $template->mime_type = $file->getMimeType();
        }

        // معالجة المتغيرات
        if ($request->filled('variables')) {
            $variables = array_map('trim', explode(',', $request->variables));
            $template->variables = $variables;
        }

        $template->save();

        return redirect()->route('document-templates.index')
            ->with('success', 'تم إنشاء القالب بنجاح');
    }

    /**
     * Display the specified document template.
     */
    public function show(DocumentTemplate $documentTemplate)
    {
        $documentTemplate->load(['creator']);
        
        return view('document_templates.show', compact('documentTemplate'));
    }

    /**
     * Show the form for editing the specified document template.
     */
    public function edit(DocumentTemplate $documentTemplate)
    {
        return view('document_templates.edit', compact('documentTemplate'));
    }

    /**
     * Update the specified document template in storage.
     */
    public function update(Request $request, DocumentTemplate $documentTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:contract,pleading,correspondence,evidence,other',
            'category' => 'required|string|in:legal,administrative,financial,evidence,correspondence',
            'content' => 'required|string',
            'variables' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
            'is_system_default' => 'boolean'
        ]);

        $documentTemplate->fill($validated);
        $documentTemplate->is_system_default = $request->boolean('is_system_default');

        // رفع ملف جديد إذا تم تحديده
        if ($request->hasFile('file')) {
            // حذف الملف القديم
            if ($documentTemplate->file_path) {
                Storage::disk('public')->delete($documentTemplate->file_path);
            }

            $file = $request->file('file');
            $filename = time() . '_' . Str::slug($validated['name']) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('document-templates', $filename, 'public');
            
            $documentTemplate->file_path = $path;
            $documentTemplate->file_name = $file->getClientOriginalName();
            $documentTemplate->file_size = $file->getSize();
            $documentTemplate->mime_type = $file->getMimeType();
        }

        // معالجة المتغيرات
        if ($request->filled('variables')) {
            $variables = array_map('trim', explode(',', $request->variables));
            $documentTemplate->variables = $variables;
        } else {
            $documentTemplate->variables = null;
        }

        $documentTemplate->save();

        return redirect()->route('document-templates.show', $documentTemplate)
            ->with('success', 'تم تحديث القالب بنجاح');
    }

    /**
     * Remove the specified document template from storage.
     */
    public function destroy(DocumentTemplate $documentTemplate)
    {
        // وضع علامة كغير نشط بدلاً من الحذف الفعلي
        $documentTemplate->update(['is_active' => false]);

        return redirect()->route('document-templates.index')
            ->with('success', 'تم حذف القالب بنجاح');
    }

    /**
     * Duplicate a document template.
     */
    public function duplicate(DocumentTemplate $documentTemplate)
    {
        $newTemplate = $documentTemplate->replicate();
        $newTemplate->name = $documentTemplate->name . ' (نسخة)';
        $newTemplate->created_by = Auth::id();
        $newTemplate->is_system_default = false;
        $newTemplate->created_at = now();
        $newTemplate->updated_at = now();
        
        $newTemplate->save();

        return redirect()->route('document-templates.edit', $newTemplate)
            ->with('success', 'تم نسخ القالب بنجاح');
    }

    /**
     * Download the template file.
     */
    public function download(DocumentTemplate $documentTemplate)
    {
        if (!$documentTemplate->file_path || !Storage::disk('public')->exists($documentTemplate->file_path)) {
            abort(404, 'الملف غير موجود');
        }

        return Storage::disk('public')->download($documentTemplate->file_path, $documentTemplate->file_name);
    }

    /**
     * Get template content for preview.
     */
    public function preview(DocumentTemplate $documentTemplate)
    {
        return response()->json([
            'content' => $documentTemplate->content,
            'variables' => $documentTemplate->variables ?? []
        ]);
    }

    /**
     * Generate document from template.
     */
    public function generate(Request $request, DocumentTemplate $documentTemplate)
    {
        $validated = $request->validate([
            'variables' => 'nullable|array',
            'case_id' => 'nullable|exists:legal_cases,id',
            'client_name' => 'nullable|string'
        ]);

        $content = $documentTemplate->content;
        
        // استبدال المتغيرات
        if (isset($validated['variables'])) {
            foreach ($validated['variables'] as $key => $value) {
                $content = str_replace('{{' . $key . '}}', $value, $content);
            }
        }

        // إنشاء مستند جديد من القالب
        $document = new \App\Models\Document([
            'title' => $documentTemplate->name . ' - ' . now()->format('Y-m-d'),
            'description' => 'تم إنشاؤه من القالب: ' . $documentTemplate->name,
            'type' => $documentTemplate->type,
            'legal_case_id' => $validated['case_id'] ?? null,
            'user_id' => Auth::id(),
            'created_by' => Auth::id()
        ]);

        // حفظ المحتوى كملف مؤقت
        $filename = time() . '_generated_' . Str::slug($documentTemplate->name) . '.html';
        $path = 'documents/' . $filename;
        Storage::disk('public')->put($path, $content);

        $document->file_path = $path;
        $document->file_name = $filename;
        $document->file_size = strlen($content);
        $document->mime_type = 'text/html';

        $document->save();

        return redirect()->route('documents.show', $document)
            ->with('success', 'تم إنشاء المستند من القالب بنجاح');
    }
}