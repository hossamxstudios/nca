<!DOCTYPE html>
@include('admin.main.html')
<head>
    <title>تفاصيل الاستيراد #{{ $import->id }} - أرشيف القاهرة الجديدة</title>
    @include('admin.main.meta')
</head>
<body>
    <div class="wrapper">
        @include('admin.main.topbar')
        @include('admin.main.sidebar')

        <div class="content-page">
            <div class="container-fluid">
                {{-- Page Header --}}
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <div class="page-title-right">
                                <ol class="m-0 breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('admin.imports.index') }}">استيراد البيانات</a></li>
                                    <li class="breadcrumb-item active">تفاصيل #{{ $import->id }}</li>
                                </ol>
                            </div>
                            <h4 class="page-title">
                                <i class="ti ti-file-import me-2"></i>
                                تفاصيل عملية الاستيراد
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Import Info Card --}}
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0 card-title text-white">
                                    <i class="ti ti-info-circle me-2"></i>معلومات الاستيراد
                                </h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted" style="width: 40%">رقم العملية:</td>
                                        <td><strong>#{{ $import->id }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">اسم الملف:</td>
                                        <td>{{ $import->original_filename }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">نوع الاستيراد:</td>
                                        <td>
                                            <span class="badge bg-info">{{ $import->type_label }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">الحالة:</td>
                                        <td>
                                            <span class="badge {{ $import->status_badge['class'] }}">
                                                <i class="ti {{ $import->status_badge['icon'] }} me-1"></i>
                                                {{ $import->status_badge['text'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">المستخدم:</td>
                                        <td>{{ $import->user?->name ?? 'غير محدد' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">تاريخ الرفع:</td>
                                        <td>{{ $import->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                    @if($import->started_at)
                                    <tr>
                                        <td class="text-muted">بداية المعالجة:</td>
                                        <td>{{ $import->started_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                    @endif
                                    @if($import->completed_at)
                                    <tr>
                                        <td class="text-muted">نهاية المعالجة:</td>
                                        <td>{{ $import->completed_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        {{-- Statistics Card --}}
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 card-title">
                                    <i class="ti ti-chart-bar me-2"></i>إحصائيات المعالجة
                                </h5>
                            </div>
                            <div class="card-body">
                                {{-- Progress Bar --}}
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>نسبة الإنجاز</span>
                                        <span>{{ $import->progress_percentage }}%</span>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-{{ $import->status === 'completed' ? 'success' : ($import->status === 'failed' ? 'danger' : 'primary') }}"
                                             style="width: {{ $import->progress_percentage }}%"></div>
                                    </div>
                                </div>

                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <div class="p-2 bg-light rounded">
                                            <h4 class="mb-0 text-primary">{{ number_format($import->total_rows ?? 0) }}</h4>
                                            <small class="text-muted">إجمالي الصفوف</small>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="p-2 bg-light rounded">
                                            <h4 class="mb-0 text-info">{{ number_format($import->processed_rows ?? 0) }}</h4>
                                            <small class="text-muted">تمت معالجتها</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 bg-success-subtle rounded">
                                            <h4 class="mb-0 text-success">{{ number_format($import->success_rows ?? 0) }}</h4>
                                            <small class="text-muted">نجحت</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 bg-danger-subtle rounded">
                                            <h4 class="mb-0 text-danger">{{ number_format($import->failed_rows ?? 0) }}</h4>
                                            <small class="text-muted">فشلت</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Actions Card --}}
                        <div class="card">
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('admin.imports.index') }}" class="btn btn-outline-secondary">
                                        <i class="ti ti-arrow-right me-1"></i>العودة للقائمة
                                    </a>
                                    @if(in_array($import->status, ['completed', 'failed']))
                                    <form action="{{ route('admin.imports.destroy', $import) }}" method="POST"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الاستيراد؟')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger w-100">
                                            <i class="ti ti-trash me-1"></i>حذف الاستيراد
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Errors Section --}}
                    <div class="col-lg-8">
                        @if(count($formattedErrors) > 0)
                        <div class="card">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0 card-title text-white">
                                    <i class="ti ti-alert-triangle me-2"></i>
                                    الأخطاء ({{ count($formattedErrors) }} خطأ)
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 80px">الصف</th>
                                                <th>العميل</th>
                                                <th>القطعة</th>
                                                <th>الملف</th>
                                                <th>الموقع</th>
                                                <th>الخطأ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($formattedErrors as $error)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $error['row'] }}</span>
                                                    <br>
                                                    <small class="text-muted">{{ $error['sheet'] }}</small>
                                                </td>
                                                <td>
                                                    <strong>{{ $error['client'] }}</strong>
                                                </td>
                                                <td>{{ $error['land'] }}</td>
                                                <td>
                                                    @if($error['file_name'] === 'لا يوجد')
                                                        <span class="text-muted">{{ $error['file_name'] }}</span>
                                                    @else
                                                        {{ $error['file_name'] }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($error['location'])
                                                        <small>{{ $error['location'] }}</small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $error['error_type'] }}-subtle text-{{ $error['error_type'] }}">
                                                        <i class="ti ti-alert-circle me-1"></i>
                                                        {{ $error['error'] }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="card">
                            <div class="card-body text-center py-5">
                                @if($import->status === 'completed')
                                <div class="avatar-lg mx-auto mb-3">
                                    <span class="avatar-title bg-success-subtle text-success rounded-circle" style="font-size: 2rem;">
                                        <i class="ti ti-check"></i>
                                    </span>
                                </div>
                                <h4 class="text-success">تم الاستيراد بنجاح!</h4>
                                <p class="text-muted">تم استيراد جميع البيانات بدون أخطاء</p>
                                @elseif($import->status === 'processing' || $import->status === 'validating')
                                <div class="avatar-lg mx-auto mb-3">
                                    <span class="avatar-title bg-warning-subtle text-warning rounded-circle" style="font-size: 2rem;">
                                        <i class="ti ti-loader ti-spin"></i>
                                    </span>
                                </div>
                                <h4 class="text-warning">جاري المعالجة...</h4>
                                <p class="text-muted">يرجى الانتظار حتى تكتمل عملية الاستيراد</p>
                                @elseif($import->status === 'pending')
                                <div class="avatar-lg mx-auto mb-3">
                                    <span class="avatar-title bg-secondary-subtle text-secondary rounded-circle" style="font-size: 2rem;">
                                        <i class="ti ti-clock"></i>
                                    </span>
                                </div>
                                <h4 class="text-secondary">في انتظار المعالجة</h4>
                                <p class="text-muted">سيتم بدء المعالجة قريباً</p>
                                @else
                                <div class="avatar-lg mx-auto mb-3">
                                    <span class="avatar-title bg-info-subtle text-info rounded-circle" style="font-size: 2rem;">
                                        <i class="ti ti-info-circle"></i>
                                    </span>
                                </div>
                                <h4>لا توجد أخطاء مسجلة</h4>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- Summary Section --}}
                        @if($import->summary && count($import->summary) > 0)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 card-title">
                                    <i class="ti ti-list-details me-2"></i>ملخص الاستيراد
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($import->summary as $key => $value)
                                    <div class="col-md-4 col-sm-6 mb-3">
                                        <div class="p-3 bg-light rounded text-center">
                                            <h4 class="mb-1">{{ number_format($value) }}</h4>
                                            <small class="text-muted">
                                                @switch($key)
                                                    @case('clients_created')
                                                        عملاء جدد
                                                        @break
                                                    @case('clients_updated')
                                                        عملاء محدثين
                                                        @break
                                                    @case('lands_created')
                                                        قطع جديدة
                                                        @break
                                                    @case('lands_updated')
                                                        قطع محدثة
                                                        @break
                                                    @case('files_created')
                                                        ملفات جديدة
                                                        @break
                                                    @case('files_updated')
                                                        ملفات محدثة
                                                        @break
                                                    @case('governorates_created')
                                                        محافظات جديدة
                                                        @break
                                                    @case('cities_created')
                                                        مدن جديدة
                                                        @break
                                                    @case('districts_created')
                                                        أحياء جديدة
                                                        @break
                                                    @case('zones_created')
                                                        مناطق جديدة
                                                        @break
                                                    @case('areas_created')
                                                        مجاورات جديدة
                                                        @break
                                                    @case('rooms_created')
                                                        غرف جديدة
                                                        @break
                                                    @case('lanes_created')
                                                        ممرات جديدة
                                                        @break
                                                    @case('stands_created')
                                                        استندات جديدة
                                                        @break
                                                    @case('racks_created')
                                                        أرفف جديدة
                                                        @break
                                                    @default
                                                        {{ $key }}
                                                @endswitch
                                            </small>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.main.scripts')

    @if(in_array($import->status, ['processing', 'validating', 'pending']))
    <script>
        // Auto-refresh page while processing
        setTimeout(function() {
            location.reload();
        }, 5000);
    </script>
    @endif
</body>
</html>
