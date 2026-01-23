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
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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
                                        <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}">العملاء</a></li>
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
                                    <div class="bg-opacity-10 bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="ti ti-user fs-2 text-primary"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-1 fw-bold">{{ $client->name }}</h4>
                                        <div class="flex-wrap gap-3 d-flex text-muted">
                                            @if($client->national_id)
                                            <span><i class="ti ti-id me-1"></i>{{ $client->national_id }}</span>
                                            @endif
                                            @if($client->telephone)
                                            <span><i class="ti ti-phone me-1"></i>{{ $client->telephone }}</span>
                                            @endif
                                            @if($client->mobile)
                                            <span><i class="ti ti-device-mobile me-1"></i>{{ $client->mobile }}</span>
                                            @endif
                                            @if($client->excel_row_number)
                                            <span><i class="ti ti-table me-1"></i>صف {{ $client->excel_row_number }}</span>
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
                                            <small class="text-muted">ملف</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-1 rounded border">
                                            <h3 class="mb-0 text-success fw-bold">{{ $client->files->sum('pages_count') }}</h3>
                                            <small class="text-muted">صفحة</small>
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

                    @if($client->files->count() > 0)
                        @foreach($client->files as $file)
                        @php
                            $fileHasMedia = $file->hasMedia('files');
                            $fileItems = $file->fileItems()->with(['item', 'media'])->get();
                            $hasFileItems = $fileItems->count() > 0;
                            $subFiles = $file->children;
                            $hasSubFiles = $subFiles->count() > 0;
                            $subFilesWithMedia = $hasSubFiles ? $subFiles->filter(fn($sf) => $sf->hasMedia('pages')) : collect();
                            $subFilesWithoutMedia = $hasSubFiles ? $subFiles->filter(fn($sf) => !$sf->hasMedia('pages')) : collect();
                            $totalExpectedPages = $file->pages_count ?? 0;
                        @endphp
                        <div class="mb-3 border-0 shadow-sm card">
                            {{-- File Card Header --}}
                            <div class="card-header bg-light">
                                <div class="flex-wrap gap-2 d-flex justify-content-between align-items-center">
                                    <div class="file-header-info">
                                        <div class="info-item">
                                            <span class="badge bg-primary fs-6">{{ $file->file_name }}</span>
                                        </div>
                                        @if($file->land)
                                        <div class="info-item">
                                            <i class="ti ti-map-pin"></i>
                                            <span>
                                                ({{ $file->land->district?->name ?? '-' }}) -> ({{ $file->land->zone?->name ?? '-' }}) -> ({{ $file->land->area?->name ?? '-' }}) -> ({{ $file->land->land_no ?? '-' }})
                                            </span>
                                        </div>
                                        @endif
                                        <div class="info-item">
                                            <i class="ti ti-building"></i>
                                            <span>
                                                (غرفة {{ $file->room?->name ?? '-' }}) -> (ممر {{ $file->lane?->name ?? '-' }}) -> (استاند {{ $file->stand?->name ?? '-' }}) -> (رف {{ $file->rack?->name ?? '-' }})
                                            </span>
                                            @role('Super Admin')
                                            <a href="javascript:void(0)" class="p-0 btn btn-mg btn-sm bg-primary-subtle ms-1" data-bs-toggle="modal" data-bs-target="#editFileLocationModal_{{ $file->id }}" title="تعديل الموقع الفعلي">
                                                <i class="ti ti-edit fs-6"></i> تعديل
                                            </a>
                                            @endrole
                                        </div>
                                    </div>
                                    <div class="gap-2 d-flex align-items-center">
                                        @if($file->barcode)
                                            <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#printBarcodeModal_{{ $file->id }}" title="طباعة الباركود">
                                                <i class="ti ti-printer"></i> طباعة الباركود
                                            </button>
                                            @endif
                                        {{-- Conditional action button in header --}}
                                        @if(!$fileHasMedia)
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadFileModal_{{ $file->id }}">
                                                <i class="ti ti-upload me-1"></i>رفع ملف
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editFileItemsModal_{{ $file->id }}">
                                             <i class="ti ti-edit me-1"></i>تعديل الملفات الفرعية
                                            </button>
                                        @endif
                                    </div>
                                    <span class="badge bg-success-subtle text-success">{{ $totalExpectedPages }} صفحة</span>
                                    @if($file->barcode)
                                        <code class="text-primary">{{ $file->barcode }}</code>
                                    @endif
                                </div>
                            </div>

                            {{-- File Card Body - SubFiles/Pages --}}
                            <div class="card-body">
                                @if($hasFileItems)
                                    {{-- Show file items with their pages --}}
                                    @php
                                        $originalPdfMedia = $file->getFirstMedia('files');
                                        $originalPdfUrl = $originalPdfMedia ? $originalPdfMedia->getUrl() : null;
                                    @endphp
                                    <div class="row g-3">
                                        @foreach($fileItems as $fileItem)
                                        <div class="col-md-4 col-lg-3">
                                            <div class="p-3 text-center rounded border">
                                                <div class="mb-2 d-flex justify-content-between align-items-start">
                                                    <h6 class="mb-0 fw-bold">{{ $fileItem->item->name ?? 'بند' }}</h6>
                                                    <span class="badge bg-secondary">ص {{ $fileItem->from_page }} - {{ $fileItem->to_page }}</span>
                                                </div>
                                                @if($originalPdfUrl)
                                                {{-- PDF Thumbnail Preview (first page of range) --}}
                                                <div class="mb-2 pdf-thumbnail-container" style="height: 120px; background: #f8f9fa; border-radius: 4px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                                    <canvas class="pdf-thumbnail" data-pdf-url="{{ $originalPdfUrl }}" data-page="{{ $fileItem->from_page }}" style="max-width: 100%; max-height: 100%;"></canvas>
                                                </div>
                                                <div class="gap-1 d-flex">
                                                    <button type="button" class="btn btn-sm btn-outline-primary flex-fill btn-preview-pdf" data-pdf-url="{{ $originalPdfUrl }}" data-from-page="{{ $fileItem->from_page }}" data-to-page="{{ $fileItem->to_page }}" data-title="{{ $fileItem->item->name ?? 'معاينة' }}">
                                                        <i class="ti ti-eye me-1"></i>معاينة
                                                    </button>
                                                    <a href="{{ route('admin.files.download-pages', $file->id) }}?from_page={{ $fileItem->from_page }}&to_page={{ $fileItem->to_page }}&filename={{ urlencode($file->file_name . '_' . ($fileItem->item->name ?? 'بند') . '_ص' . $fileItem->from_page . '-' . $fileItem->to_page) }}" class="btn btn-sm btn-outline-success flex-fill">
                                                        <i class="ti ti-download me-1"></i>تحميل
                                                    </a>
                                                </div>
                                                @else
                                                <div class="py-4 text-muted small">
                                                    <i class="mb-2 ti ti-file-off fs-3 d-block"></i>
                                                    لا يوجد ملف مرفق
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @elseif($hasSubFiles)
                                    {{-- Show subfiles with media --}}
                                    @if($subFilesWithMedia->count() > 0)
                                    <div class="row g-3">
                                        @foreach($subFilesWithMedia as $subFile)
                                        <div class="col-auto">
                                            <div class="page-card">
                                                @php
                                                    $media = $subFile->getFirstMedia('pages');
                                                    $thumbnailUrl = $media ? $media->getUrl('thumb') : '';
                                                @endphp
                                                <img src="{{ $thumbnailUrl }}" alt="صفحة {{ $subFile->page_number ?? $subFile->file_name }}" class="mb-2 page-thumbnail" onerror="this.style.display='none'">
                                                <div class="mb-2 small fw-semibold">
                                                    @if($subFile->page_number)
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
                                    @if($subFilesWithoutMedia->count() > 0)
                                    <div class="@if($subFilesWithMedia->count() > 0) mt-3 @endif alert alert-info mb-0 d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="ti ti-photo me-2"></i>
                                            يوجد <strong>{{ $subFilesWithoutMedia->count() }}</strong> ملف فرعي بدون صور
                                        </div>
                                        <button type="button" class="btn btn-info btn-sm btn-select-pages" data-file-id="{{ $file->id }}">
                                            <i class="ti ti-photo-plus me-1"></i>اختيار الصفحات
                                        </button>
                                    </div>
                                    @endif
                                @else
                                    {{-- No subfiles yet --}}
                                    <div class="py-4 text-center text-muted">
                                        @if(!$fileHasMedia)
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
                    <span id="pageInfo" class="badge bg-secondary ms-2"><span id="totalPagesNum">0</span> صفحة</span>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="p-3 modal-body" id="pdfPagesContainer" style="height: 80vh; background: #525659; overflow-y: auto;">
                    <div class="text-center text-white">
                        <div class="spinner-border" role="status"></div>
                        <p class="mt-2">جاري تحميل الصفحات...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        document.addEventListener('DOMContentLoaded', function() {
            // Render PDF thumbnails
            document.querySelectorAll('.pdf-thumbnail').forEach(function(canvas) {
                const pdfUrl = canvas.dataset.pdfUrl;
                const pageNum = parseInt(canvas.dataset.page) || 1;
                if (pdfUrl) {
                    renderPdfThumbnail(pdfUrl, canvas, pageNum);
                }
            });

            // Handle preview button click
            document.querySelectorAll('.btn-preview-pdf').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const pdfUrl = this.dataset.pdfUrl;
                    const title = this.dataset.title || 'معاينة';
                    const fromPage = parseInt(this.dataset.fromPage) || 1;
                    const toPage = parseInt(this.dataset.toPage) || 1;

                    document.getElementById('pdfPreviewModalTitle').textContent = title + ' (ص ' + fromPage + ' - ' + toPage + ')';
                    document.getElementById('totalPagesNum').textContent = (toPage - fromPage + 1);

                    openPdfPreviewScrollable(pdfUrl, fromPage, toPage);

                    const modal = new bootstrap.Modal(document.getElementById('pdfPreviewModal'));
                    modal.show();
                });
            });

            // Clear on modal close
            document.getElementById('pdfPreviewModal').addEventListener('hidden.bs.modal', function() {
                document.getElementById('pdfPagesContainer').innerHTML = `
                    <div class="text-center text-white">
                        <div class="spinner-border" role="status"></div>
                        <p class="mt-2">جاري تحميل الصفحات...</p>
                    </div>
                `;
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
                pageWrapper.style.cssText = 'background: white; border-radius: 4px; padding: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.3);';

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
                const viewport = page.getViewport({ scale: 1 });
                const scale = Math.min(containerWidth / viewport.width, 1.5);
                const scaledViewport = page.getViewport({ scale: scale });

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

        function renderPdfThumbnail(pdfUrl, canvas, pageNum) {
            pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
                pdf.getPage(pageNum).then(function(page) {
                    const containerHeight = 120;
                    const viewport = page.getViewport({ scale: 1 });
                    const scale = containerHeight / viewport.height;
                    const scaledViewport = page.getViewport({ scale: scale });

                    canvas.height = scaledViewport.height;
                    canvas.width = scaledViewport.width;

                    const context = canvas.getContext('2d');
                    page.render({
                        canvasContext: context,
                        viewport: scaledViewport
                    });
                });
            }).catch(function(error) {
                console.error('Error loading PDF thumbnail:', error);
            });
        }
    </script>
</body>
</html>
