<div class="mb-1 border-0 card">
    <div class="py-2 card-body">
        <form action="{{ route('admin.clients.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-3">
                <select name="search_field" class="form-select" >
                    <optgroup label="بيانات العميل">
                        <option value="name" {{ ($searchField ?? 'name') == 'name' ? 'selected' : '' }}>الاسم</option>
                        <option value="national_id" {{ ($searchField ?? '') == 'national_id' ? 'selected' : '' }}>الرقم القومي</option>
                    </optgroup>
                    <optgroup label="بيانات الملفات">
                        <option value="file_name" {{ ($searchField ?? '') == 'file_name' ? 'selected' : '' }}>رقم الملف</option>
                    </optgroup>
                    <optgroup label="بيانات الأراضي">
                        <option value="land_no" {{ ($searchField ?? '') == 'land_no' ? 'selected' : '' }}>رقم الأرض</option>
                    </optgroup>
                </select>
            </div>
            <div class="col-md-7">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light">
                        <i class="ti ti-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control" placeholder="ابحث هنا..." value="{{ $searchTerm ?? '' }}">
                    @if($searchTerm ?? false)
                    <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-x"></i>
                    </a>
                    @endif
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="ti ti-search me-1"></i>بحث
                </button>
            </div>
        </form>
    </div>
</div>
