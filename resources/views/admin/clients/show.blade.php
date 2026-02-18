<!DOCTYPE html>
@include('admin.main.html')

<head>
    <title>تفاصيل العميل - {{ $client->name }}</title>
    @include('admin.main.meta')
    <style>
        .page-thumbnail {
            width: 80px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #dee2e6;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .page-thumbnail:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .page-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
        }

        .file-header-info {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            align-items: center;
        }

        .file-header-info .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .file-header-info .info-item i {
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        @include('admin.main.topbar')
        @include('admin.main.sidebar')
        <div class="content-page">
            <div class="container-fluid">
                {{-- Header --}}
                <div class="pt-3 mb-2 row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="pb-0 mb-0 fw-bold">
                                    <i class="ti ti-user me-2"></i>تفاصيل العميل
                                </h4>
                                <nav aria-label="breadcrumb">
                                    <ol class="p-1 mb-0 breadcrumb">
                                        <li class="breadcrumb-item"><a
                                                href="{{ route('admin.clients.index') }}">العملاء</a></li>
                                        <li class="breadcrumb-item active">{{ $client->name }}</li>
                                    </ol>
                                </nav>
                            </div>
                            <a href="{{ route('admin.clients.index') }}" class="btn btn-primary">
                                <i class="ti ti-arrow-right me-1"></i>رجوع
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Client Info Section - Full Width --}}
                <div class="mb-3 border-0 shadow card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="gap-4 d-flex align-items-center">
                                    <div class="bg-opacity-10 bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 60px; height: 60px;">
                                        <i class="ti ti-user fs-2 text-primary"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-1 fw-bold">{{ $client->name }}</h4>
                                        <div class="flex-wrap gap-3 d-flex text-dark">
                                            @if ($client->national_id)
                                                <span><i class="ti ti-id me-1"></i>{{ $client->national_id }}</span>
                                            @endif
                                            @if ($client->telephone)
                                                <span><i class="ti ti-phone me-1"></i>{{ $client->telephone }}</span>
                                            @endif
                                            @if ($client->mobile)
                                                <span><i class="ti ti-device-mobile me-1"></i>{{ $client->mobile }}</span>
                                            @endif
                                            @if ($client->excel_row_number)
                                                <span><i class="ti ti-table me-1 text-dark"></i>صف {{ $client->excel_row_number }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center row">
                                    <div class="border-0 col-6">
                                        <div class="p-1 rounded border">
                                            <h3 class="mb-0 text-primary fw-bold">{{ $client->files->count() }}</h3>
                                            <span class="text-dark">ملف</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-1 rounded border">
                                            <h3 class="mb-0 text-dark fw-bold">
                                                {{ $client->files->sum('pages_count') }}</h3>
                                            <span class="text-dark">صفحة</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Files Section - Full Width --}}
                <div class="mb-3">
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="ti ti-folders me-2"></i>الملفات
                            <span class="badge bg-primary ms-2">{{ $client->files->count() }}</span>
                        </h5>
                    </div>

                    @if ($client->files->count() > 0)
                        @foreach ($client->files as $file)
                            @php
                                $fileHasMedia = $file->hasMedia('files');
                                $fileItems = $file->fileItems()->with(['item', 'media'])->get();
                                $hasFileItems = $fileItems->count() > 0;
                                $subFiles = $file->children;
                                $hasSubFiles = $subFiles->count() > 0;
                                $subFilesWithMedia = $hasSubFiles
                                    ? $subFiles->filter(fn($sf) => $sf->hasMedia('pages'))
                                    : collect();
                                $subFilesWithoutMedia = $hasSubFiles
                                    ? $subFiles->filter(fn($sf) => !$sf->hasMedia('pages'))
                                    : collect();
                                $totalExpectedPages = $file->pages_count ?? 0;
                            @endphp
                            <div class="mb-3 border-0 shadow-sm card">
                                {{-- File Card Header --}}
                                <div class="card-header bg-light">
                                    <div class="flex-wrap gap-2 d-flex justify-content-between align-items-center">
                                        <div class="file-header-info">
                                            <div class="info-item">
                                                <span class="badge bg-primary fs-5">{{ $file->file_name }}</span>
                                                @can('clients.edit')
                                                    <button type="button" class="p-0 px-1 btn btn-outline-secondary ms-1" data-bs-toggle="modal" data-bs-target="#editFileNameModal_{{ $file->id }}" title="تعديل رقم الملف">
                                                        <i class="ti ti-edit fs-4"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                            @if ($fileHasMedia)
                                                @php
                                                    $fullPdfUrl = $file->getFirstMediaUrl('files');
                                                @endphp
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary btn-preview-pdf" title="معاينة الملف كامل" data-pdf-url="{{ $fullPdfUrl }}" data-from-page="1" data-to-page="{{ $file->pages_count ?: 1 }}" data-title="معاينة: {{ $file->file_name }}" data-client-id="{{ $client->id }}" data-client-name="{{ $client->name }}" data-file-name="{{ $file->file_name }}" data-item-name="الملف الكامل">
                                                        <i class="ti ti-eye me-1"></i>معاينة
                                                    </button>
                                                    <a href="{{ route('admin.files.download-original', $file->id) }}" class="btn btn-outline-primary" title="تحميل الملف الأصلي">
                                                        <i class="ti ti-download me-1"></i>تحميل
                                                    </a>
                                                    @can('clients.delete')
                                                        <button type="button" class="btn btn-outline-danger btn-clear-media" title="مسح الملف المرفوع" data-file-id="{{ $file->id }}" data-file-name="{{ $file->file_name }}">
                                                            <i class="ti ti-trash me-1"></i>مسح
                                                        </button>
                                                    @endcan
                                                </div>
                                            @endif
                                            @if ($file->land)
                                                <div
                                                    class="gap-2 px-3 py-1 bg-white border d-flex align-items-center rounded-pill">
                                                    <i class="ti ti-map-pin text-danger"></i>
                                                    <span class="text-dark fs-5 fw-bold">
                                                        {{ $file->land->district?->name ?? '-' }}
                                                        <i class="mx-1 ti ti-chevron-right fs-5 text-dark"></i>
                                                        {{ $file->land->zone?->name ?? '-' }}
                                                        <i class="mx-1 ti ti-chevron-right fs-5 text-dark"></i>
                                                        {{ $file->land->area?->name ?? '-' }}
                                                        <i class="mx-1 ti ti-chevron-right fs-5 text-dark"></i>
                                                        <strong>{{ $file->land->land_no ?? '-' }}</strong>
                                                    </span>
                                                    @role('Super Admin')
                                                    <button type="button" class="p-0 px-1 border-0 btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editFileLocationModal_{{ $file->id }}" title="تعديل الموقع">
                                                        <i class="ti ti-edit text-primary fs-4"></i>
                                                    </button>
                                                    @endrole
                                                </div>
                                            @endif
                                            <div
                                                class="gap-2 px-3 py-1 bg-white border d-flex align-items-center rounded-pill">
                                                <i class="ti ti-building text-info"></i>
                                                <span class="text-dark fs-5 fw-bold">
                                                    {{ $file->room?->name ?? '-' }} غرفة
                                                    <i class="mx-1 ti ti-chevron-right fs-5 text-dark"></i>
                                                    {{ $file->lane?->name ?? '-' }} ممر
                                                    <i class="mx-1 ti ti-chevron-right fs-5 text-dark"></i>
                                                    {{ $file->stand?->name ?? '-' }} ستاند
                                                    <i class="mx-1 ti ti-chevron-right fs-5 text-dark"></i>
                                                    <strong>{{ $file->rack?->name ?? '-' }} رف</strong>
                                                </span>
                                                @role('Super Admin')
                                                    <button type="button" class="p-0 px-1 border-0 btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editFileLocationModal_{{ $file->id }}" title="تعديل الموقع">
                                                        <i class="ti ti-edit text-primary fs-4"></i>
                                                    </button>
                                                @endrole
                                            </div>
                                        </div>
                                        <div class="gap-2 d-flex align-items-center">
                                            @if ($file->barcode)
                                                <button type="button" class="btn btn-outline-dark btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#printBarcodeModal_{{ $file->id }}"
                                                    title="طباعة الباركود">
                                                    <i class="ti ti-printer"></i> طباعة الباركود
                                                </button>
                                            @endif

                                            {{-- Conditional action button in header --}}
                                            @can('clients.create')
                                                @if (!$fileHasMedia)
                                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadFileModal_{{ $file->id }}">
                                                        <i class="ti ti-upload me-1"></i>رفع ملف
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editFileItemsModal_{{ $file->id }}">
                                                        <i class="ti ti-edit me-1"></i>تعديل الملفات الفرعية
                                                    </button>
                                                @endif
                                            @endcan
                                        </div>
                                        <span class="badge bg-primary-subtle fs-6 text-dark">{{ $totalExpectedPages }} صفحة</span>
                                        @if ($file->barcode)
                                            <code class="text-primary">{{ $file->barcode }}</code>
                                        @endif
                                    </div>
                                </div>
                                {{-- File Card Body - SubFiles/Pages --}}
                                <div class="card-body">
                                    @if ($hasFileItems)
                                        {{-- Show file items with their pages --}}
                                        @php
                                            $originalPdfMedia = $file->getFirstMedia('files');
                                            $originalPdfUrl = $originalPdfMedia ? $originalPdfMedia->getUrl() : null;
                                        @endphp
                                        <div class="row g-3">
                                            @foreach ($fileItems as $fileItem)
                                                <div class="col-md-4 col-lg-3">
                                                    <div class="p-3 text-center rounded border">
                                                        <div class="mb-2 d-flex justify-content-between align-items-start">
                                                            <h6 class="mb-0 fw-bold fs-4">
                                                                {{ $fileItem->item->name ?? 'بند' }}</h6>
                                                            <span class="badge bg-primary fs-5">ص
                                                                {{ $fileItem->from_page }} -
                                                                {{ $fileItem->to_page }}</span>
                                                        </div>
                                                        @if ($originalPdfUrl)
                                                            {{-- PDF Thumbnail Preview (first page of range) --}}
                                                            <div class="mb-2 pdf-thumbnail-container"
                                                                style="height: 120px; background: #f8f9fa; border-radius: 4px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                                                <canvas class="pdf-thumbnail" data-pdf-url="{{ $originalPdfUrl }}" data-page="{{ $fileItem->from_page }}" style="max-width: 100%; max-height: 100%;"></canvas>
                                                            </div>
                                                            <div class="gap-1 d-flex">
                                                                <button type="button" class="btn btn-sm btn-outline-primary flex-fill btn-preview-pdf" data-pdf-url="{{ $originalPdfUrl }}" data-from-page="{{ $fileItem->from_page }}" data-to-page="{{ $fileItem->to_page }}" data-title="{{ $fileItem->item->name ?? 'معاينة' }}" data-client-id="{{ $client->id }}" data-client-name="{{ $client->name }}" data-file-name="{{ $file->file_name }}" data-item-name="{{ $fileItem->item->name ?? 'بند' }}">
                                                                    <i class="ti ti-eye me-1"></i>معاينة
                                                                </button>
                                                                <a href="{{ route('admin.files.download-pages', $file->id) }}?from_page={{ $fileItem->from_page }}&to_page={{ $fileItem->to_page }}&filename={{ urlencode($file->file_name . '_' . ($fileItem->item->name ?? 'بند') . '_ص' . $fileItem->from_page . '-' . $fileItem->to_page) }}" class="btn btn-sm btn-outline-primary flex-fill">
                                                                    <i class="ti ti-download me-1"></i>تحميل
                                                                </a>
                                                            </div>
                                                        @else
                                                            <div class="py-4 text-muted small">
                                                                <i class="mb-2 ti ti-file-off fs-3 d-block"></i> لا يوجد ملف مرفق
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif($hasSubFiles)
                                        {{-- Show subfiles with media --}}
                                        @if ($subFilesWithMedia->count() > 0)
                                            <div class="row g-3">
                                                @foreach ($subFilesWithMedia as $subFile)
                                                    <div class="col-auto">
                                                        <div class="page-card">
                                                            @php
                                                                $media = $subFile->getFirstMedia('pages');
                                                                $thumbnailUrl = $media ? $media->getUrl('thumb') : '';
                                                            @endphp
                                                            <img src="{{ $thumbnailUrl }}" alt="صفحة {{ $subFile->page_number ?? $subFile->file_name }}" class="mb-2 page-thumbnail" onerror="this.style.display='none'">
                                                            <div class="mb-2 fw-semibold">
                                                                @if ($subFile->page_number)
                                                                    صفحة {{ $subFile->page_number }}
                                                                @else
                                                                    {{ $subFile->file_name }}
                                                                @endif
                                                            </div>
                                                            <div class="btn-group btn-group-sm">
                                                                <button type="button" class="btn btn-outline-primary btn-preview-page" title="معاينة" data-page-id="{{ $subFile->id }}">
                                                                    <i class="ti ti-eye"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-outline-success btn-download-page" title="تحميل" data-page-id="{{ $subFile->id }}">
                                                                    <i class="ti ti-download"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-outline-warning btn-replace-page" title="استبدال" data-page-id="{{ $subFile->id }}">
                                                                    <i class="ti ti-refresh"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        {{-- Subfiles without media notice --}}
                                        @if ($subFilesWithoutMedia->count() > 0)
                                            <div
                                                class="@if ($subFilesWithMedia->count() > 0) mt-3 @endif alert alert-info mb-0 d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="ti ti-photo me-2"></i>
                                                    يوجد <strong>{{ $subFilesWithoutMedia->count() }}</strong> ملف فرعي
                                                    بدون صور
                                                </div>
                                                <button type="button" class="btn btn-info btn-sm btn-select-pages"
                                                    data-file-id="{{ $file->id }}">
                                                    <i class="ti ti-photo-plus me-1"></i>اختيار الصفحات
                                                </button>
                                            </div>
                                        @endif
                                    @else
                                        {{-- No subfiles yet --}}
                                        <div class="py-4 text-center text-muted">
                                            @if (!$fileHasMedia)
                                                <i class="ti ti-file-off fs-1"></i>
                                                <p class="mt-2 mb-0">لم يتم رفع ملف بعد</p>
                                            @else
                                                <i class="ti ti-files fs-1"></i>
                                                <p class="mt-2 mb-0">لم يتم تحديد الملفات الفرعية بعد</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Edit File Name Modal --}}
                            <div class="modal fade" id="editFileNameModal_{{ $file->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.files.update-name', $file->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title"><i class="ti ti-edit me-1"></i>تعديل رقم الملف
                                                </h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-0">
                                                    <label class="form-label">رقم الملف</label>
                                                    <input type="text" class="form-control" name="file_name"
                                                        value="{{ $file->file_name }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">إلغاء</button>
                                                <button type="submit" class="btn btn-primary">حفظ</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="border-0 shadow-sm card">
                            <div class="py-5 text-center card-body text-muted">
                                <i class="ti ti-folder-off fs-1"></i>
                                <p class="mt-2 mb-3">لا توجد ملفات لهذا العميل</p>
                                @can('clients.create')
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#showAddFileModal">
                                        <i class="ti ti-file-plus me-1"></i>إضافة ملف جديد
                                    </button>
                                @endcan
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('admin.main.scripts')
    @include('admin.clients.upload-modal')
    @include('admin.clients.edit-file-items-modal')
    @include('admin.clients.show-print-barcode-modal')
    @include('admin.clients.edit-file-location-modal')
    @can('clients.create')
        @include('admin.clients.show-add-file-modal')
    @endcan

    {{-- PDF Preview Modal --}}
    <div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfPreviewModalTitle">معاينة</h5>
                    <span id="pageInfo" class="badge bg-secondary ms-2"><span id="totalPagesNum">0</span>
                        صفحة</span>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"
                        aria-label="إغلاق"></button>
                </div>
                <div class="p-3 modal-body" id="pdfPagesContainer"
                    style="height: 80vh; background: #525659; overflow-y: auto;">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        document.addEventListener('DOMContentLoaded', function() {
            // Lazy load PDF thumbnails using IntersectionObserver
            const thumbnailObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const canvas = entry.target;
                        const pdfUrl = canvas.dataset.pdfUrl;
                        const pageNum = parseInt(canvas.dataset.page) || 1;
                        if (pdfUrl && !canvas.dataset.loaded) {
                            canvas.dataset.loaded = 'true';
                            renderPdfThumbnail(pdfUrl, canvas, pageNum);
                        }
                        observer.unobserve(canvas);
                    }
                });
            }, {
                rootMargin: '100px', // Start loading slightly before visible
                threshold: 0.1
            });

            // Observe all PDF thumbnails
            document.querySelectorAll('.pdf-thumbnail').forEach(function(canvas) {
                // Add loading placeholder
                const container = canvas.parentElement;
                if (container && !container.querySelector('.thumbnail-loader')) {
                    const loader = document.createElement('div');
                    loader.className = 'thumbnail-loader position-absolute top-50 start-50 translate-middle';
                    loader.innerHTML = '<div class="spinner-border spinner-border-sm text-secondary" role="status"></div>';
                    container.style.position = 'relative';
                    container.appendChild(loader);
                }
                thumbnailObserver.observe(canvas);
            });

            // Handle preview button click
            document.querySelectorAll('.btn-preview-pdf').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const pdfUrl = this.dataset.pdfUrl;
                    const title = this.dataset.title || 'معاينة';
                    const fromPage = parseInt(this.dataset.fromPage) || 1;
                    const toPage = parseInt(this.dataset.toPage) || 1;
                    const clientId = this.dataset.clientId;
                    const clientName = this.dataset.clientName;
                    const fileName = this.dataset.fileName;
                    const itemName = this.dataset.itemName;

                    document.getElementById('pdfPreviewModalTitle').textContent = title + ' (ص ' +
                        fromPage + ' - ' + toPage + ')';
                    document.getElementById('totalPagesNum').textContent = (toPage - fromPage + 1);

                    const modalEl = document.getElementById('pdfPreviewModal');
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();

                    // Load PDF after modal is fully shown to get correct container width
                    modalEl.addEventListener('shown.bs.modal', function onShown() {
                        modalEl.removeEventListener('shown.bs.modal', onShown);
                        openPdfPreviewScrollable(pdfUrl, fromPage, toPage);
                    });

                    // Log view activity
                    if (clientId) {
                        fetch('{{ route('admin.clients.log-view') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                client_id: clientId,
                                client_name: clientName,
                                file_name: fileName,
                                item_name: itemName,
                                from_page: fromPage,
                                to_page: toPage
                            })
                        });
                    }
                });
            });

            // Clear on modal close
            document.getElementById('pdfPreviewModal').addEventListener('hidden.bs.modal', function() {
                document.getElementById('pdfPagesContainer').innerHTML = '';
            });
        });

        function openPdfPreviewScrollable(pdfUrl, fromPage, toPage) {
            const container = document.getElementById('pdfPagesContainer');

            pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
                container.innerHTML = '';

                // Render all pages in range
                const pagePromises = [];
                for (let pageNum = fromPage; pageNum <= toPage; pageNum++) {
                    pagePromises.push(renderScrollablePage(pdf, pageNum, fromPage, container));
                }

                Promise.all(pagePromises).then(() => {
                    console.log('All pages rendered');
                });
            }).catch(function(error) {
                console.error('Error loading PDF:', error);
                container.innerHTML = '<div class="text-center text-danger"><p>حدث خطأ أثناء تحميل الملف</p></div>';
            });
        }

        function renderScrollablePage(pdf, pageNum, fromPage, container) {
            return pdf.getPage(pageNum).then(function(page) {
                // Create page wrapper
                const pageWrapper = document.createElement('div');
                pageWrapper.className = 'pdf-page-wrapper text-center mb-3';
                pageWrapper.style.cssText =
                    'background: white; border-radius: 4px; padding: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.3);';

                // Page number label
                const pageLabel = document.createElement('div');
                pageLabel.className = 'text-muted small mb-2';
                pageLabel.textContent = 'صفحة ' + (pageNum - fromPage + 1);
                pageWrapper.appendChild(pageLabel);

                // Canvas for page
                const canvas = document.createElement('canvas');
                canvas.style.cssText = 'max-width: 100%; display: block; margin: 0 auto;';
                pageWrapper.appendChild(canvas);

                // Calculate scale to fit container width
                const containerWidth = container.clientWidth - 40;
                const viewport = page.getViewport({
                    scale: 1
                });
                const scale = Math.min(containerWidth / viewport.width, 1.5);
                const scaledViewport = page.getViewport({
                    scale: scale
                });

                canvas.height = scaledViewport.height;
                canvas.width = scaledViewport.width;

                const context = canvas.getContext('2d');

                // Append to container in order
                const existingPages = container.querySelectorAll('.pdf-page-wrapper');
                let inserted = false;
                for (let i = 0; i < existingPages.length; i++) {
                    const existingPageNum = parseInt(existingPages[i].dataset.pageNum);
                    if (pageNum < existingPageNum) {
                        container.insertBefore(pageWrapper, existingPages[i]);
                        inserted = true;
                        break;
                    }
                }
                if (!inserted) {
                    container.appendChild(pageWrapper);
                }
                pageWrapper.dataset.pageNum = pageNum;

                return page.render({
                    canvasContext: context,
                    viewport: scaledViewport
                }).promise;
            });
        }

        // PDF Document Cache to avoid re-downloading
        const pdfDocumentCache = new Map();
        const canvasRenderingState = new WeakMap();

        async function getCachedPdfDocument(pdfUrl, onProgress = null) {
            if (pdfDocumentCache.has(pdfUrl)) {
                return pdfDocumentCache.get(pdfUrl);
            }
            const loadingTask = pdfjsLib.getDocument({
                url: pdfUrl,
                cMapUrl: 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/cmaps/',
                cMapPacked: true,
                disableAutoFetch: true,  // Load pages on demand, not all at once
                disableStream: false,    // Allow streaming for faster initial load
            });

            // Track download progress
            if (onProgress) {
                loadingTask.onProgress = function(progress) {
                    if (progress.total > 0) {
                        const percent = Math.round((progress.loaded / progress.total) * 100);
                        onProgress(percent);
                    }
                };
            }

            const pdf = await loadingTask.promise;
            pdfDocumentCache.set(pdfUrl, pdf);
            return pdf;
        }

        async function renderPdfThumbnail(pdfUrl, canvas, pageNum) {
            // Prevent multiple renders on same canvas
            if (canvasRenderingState.get(canvas)) {
                return;
            }
            canvasRenderingState.set(canvas, true);

            const container = canvas.parentElement;
            const loader = container?.querySelector('.thumbnail-loader');

            try {
                const pdf = await getCachedPdfDocument(pdfUrl);
                const page = await pdf.getPage(pageNum);

                // Use lower resolution for faster rendering (80px height instead of 120)
                const containerHeight = 80;
                const viewport = page.getViewport({ scale: 1 });
                const scale = containerHeight / viewport.height;
                const scaledViewport = page.getViewport({ scale: scale });

                canvas.height = scaledViewport.height;
                canvas.width = scaledViewport.width;

                const context = canvas.getContext('2d');
                await page.render({
                    canvasContext: context,
                    viewport: scaledViewport
                }).promise;

                // Remove loader on success
                if (loader) loader.remove();
            } catch (error) {
                console.error('Error loading PDF thumbnail:', error);
                // Show error icon instead of loader
                if (loader) {
                    loader.innerHTML = '<i class="ti ti-photo-off text-muted"></i>';
                }
            } finally {
                canvasRenderingState.set(canvas, false);
            }
        }

        // Clear media button handler
        document.querySelectorAll('.btn-clear-media').forEach(btn => {
            btn.addEventListener('click', function() {
                const fileId = this.dataset.fileId;
                const fileName = this.dataset.fileName;

                if (confirm(
                        `هل أنت متأكد من مسح الملف المرفوع "${fileName}"؟\n\nسيتم حذف:\n- الملف PDF المرفوع\n- البنود المحددة\n\nلن يتم حذف:\n- المكان الجغرافي\n- المكان الفعلي`
                    )) {
                    // Create and submit form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/files/${fileId}/clear-media`;
                    form.innerHTML = `
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    </script>
    <script>
        let currentPdfLoadController = null;

        // Optimized scrollable preview with lazy loading
        async function openPdfPreviewScrollableOptimized(pdfUrl, fromPage, toPage) {
            const container = document.getElementById('pdfPagesContainer');

            // Cancel any ongoing load
            if (currentPdfLoadController) {
                currentPdfLoadController.abort = true;
            }
            currentPdfLoadController = { abort: false };
            const controller = currentPdfLoadController;

            try {
                // Show loading with progress
                container.innerHTML = `
                    <div class="text-center text-white" id="pdfLoadingIndicator">
                        <div class="spinner-border" role="status"></div>
                        <p class="mt-2">جاري تحميل الملف...</p>
                        <div class="mt-2 progress" style="width: 200px; margin: 0 auto; height: 6px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" id="pdfLoadProgress" style="width: 0%"></div>
                        </div>
                        <small class="mt-1 text-white-50 d-block" id="pdfLoadPercent">0%</small>
                    </div>
                `;

                const pdf = await getCachedPdfDocument(pdfUrl, (percent) => {
                    const progressBar = document.getElementById('pdfLoadProgress');
                    const percentText = document.getElementById('pdfLoadPercent');
                    if (progressBar) progressBar.style.width = percent + '%';
                    if (percentText) percentText.textContent = percent + '%';
                });

                if (controller.abort) return;

                container.innerHTML = '';

                const totalPages = toPage - fromPage + 1;
                let renderedCount = 0;

                // Create placeholders for all pages first (for correct ordering)
                for (let pageNum = fromPage; pageNum <= toPage; pageNum++) {
                    const pageWrapper = document.createElement('div');
                    pageWrapper.className = 'pdf-page-wrapper text-center mb-3';
                    pageWrapper.id = `pdf-page-${pageNum}`;
                    pageWrapper.dataset.pageNum = pageNum;
                    pageWrapper.style.cssText = 'background: white; border-radius: 4px; padding: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.3); min-height: 200px;';
                    pageWrapper.innerHTML = `
                        <div class="mb-2 text-muted small">صفحة ${pageNum - fromPage + 1}</div>
                        <div class="d-flex align-items-center justify-content-center" style="height: 150px;">
                            <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
                        </div>
                    `;
                    container.appendChild(pageWrapper);
                }

                // Lazy load pages using IntersectionObserver for better performance
                const pageObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const pageWrapper = entry.target;
                            const pageNum = parseInt(pageWrapper.dataset.pageNum);
                            if (!pageWrapper.dataset.rendered) {
                                pageWrapper.dataset.rendered = 'true';
                                renderPageInPlaceholder(pdf, pageNum, fromPage, container);
                            }
                            pageObserver.unobserve(pageWrapper);
                        }
                    });
                }, {
                    root: container,
                    rootMargin: '200px', // Pre-load pages 200px before visible
                    threshold: 0.1
                });

                // Observe all page placeholders
                container.querySelectorAll('.pdf-page-wrapper').forEach(wrapper => {
                    pageObserver.observe(wrapper);
                });

                // Render first 2 pages immediately for instant feedback
                const initialPages = Math.min(2, toPage - fromPage + 1);
                for (let i = 0; i < initialPages; i++) {
                    const pageNum = fromPage + i;
                    const wrapper = document.getElementById(`pdf-page-${pageNum}`);
                    if (wrapper && !wrapper.dataset.rendered) {
                        wrapper.dataset.rendered = 'true';
                        pageObserver.unobserve(wrapper);
                        await renderPageInPlaceholder(pdf, pageNum, fromPage, container);
                    }
                }

            } catch (error) {
                console.error('Error loading PDF:', error);
                if (!controller.abort) {
                    container.innerHTML = `
                        <div class="text-center text-white">
                            <i class="ti ti-alert-circle fs-1 text-danger"></i>
                            <p class="mt-2">حدث خطأ أثناء تحميل الملف</p>
                            <button class="btn btn-sm btn-outline-light" onclick="openPdfPreviewScrollableOptimized('${pdfUrl}', ${fromPage}, ${toPage})">
                                <i class="ti ti-refresh me-1"></i>إعادة المحاولة
                            </button>
                        </div>
                    `;
                }
            }
        }

        async function renderPageInPlaceholder(pdf, pageNum, fromPage, container) {
            try {
                const page = await pdf.getPage(pageNum);
                const pageWrapper = document.getElementById(`pdf-page-${pageNum}`);
                if (!pageWrapper) return;

                // Clear placeholder
                pageWrapper.innerHTML = '';

                // Page number label
                const pageLabel = document.createElement('div');
                pageLabel.className = 'text-muted small mb-2';
                pageLabel.textContent = 'صفحة ' + (pageNum - fromPage + 1);
                pageWrapper.appendChild(pageLabel);

                // Canvas for page
                const canvas = document.createElement('canvas');
                canvas.style.cssText = 'max-width: 100%; display: block; margin: 0 auto;';
                pageWrapper.appendChild(canvas);

                // Calculate scale - use lower scale (max 1.0) for faster rendering
                const containerWidth = container.clientWidth - 40;
                const viewport = page.getViewport({ scale: 1 });
                const scale = Math.min(containerWidth / viewport.width, 1.0);
                const scaledViewport = page.getViewport({ scale: scale });

                canvas.height = scaledViewport.height;
                canvas.width = scaledViewport.width;
                pageWrapper.style.minHeight = 'auto';

                const context = canvas.getContext('2d');
                await page.render({
                    canvasContext: context,
                    viewport: scaledViewport
                }).promise;

            } catch (error) {
                console.error(`Error rendering page ${pageNum}:`, error);
                // Show error state
                const pageWrapper = document.getElementById(`pdf-page-${pageNum}`);
                if (pageWrapper) {
                    pageWrapper.innerHTML = `
                        <div class="mb-2 text-muted small">صفحة ${pageNum - fromPage + 1}</div>
                        <div class="py-3 text-center text-danger">
                            <i class="ti ti-alert-circle"></i> خطأ في تحميل الصفحة
                        </div>
                    `;
                }
            }
        }

        // Override the original preview function
        window.openPdfPreviewScrollable = openPdfPreviewScrollableOptimized;
    </script>
</body>

</html>
