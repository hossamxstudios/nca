<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use App\Models\File;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class PageController extends Controller {
    public function index(Request $request) {

        if(Auth::user()->hasRole('Super Admin')){
            $totalClients = Client::count();
            $totalPages = File::sum('pages_count');
            $totalFiles = File::whereNull('parent_id')->count();
            return view('admin.dashboard.index', compact(
                'totalClients',
                'totalPages',
                'totalFiles'
            ));
        }else{
            $totalClients = Client::count();
            $totalFiles = File::whereNull('parent_id')->count();
            $totalPages = File::sum('pages_count');

            // Search parameters
            $searchTerm = $request->input('search');
            $searchField = $request->input('search_field', 'name');
            $barcode = $request->input('barcode');

            // Build query with eager loading
            $query = Client::with([
                'files' => function ($q) {
                    $q->whereNull('parent_id')
                    ->with(['land.district', 'land.zone', 'land.area', 'room', 'lane', 'stand', 'rack']);
                },
                'lands'
            ]);

            // Apply search filters
            if ($searchTerm) {
                switch ($searchField) {
                    case 'name':
                        $query->where('name', 'like', "%{$searchTerm}%");
                        break;
                    case 'mobile':
                        $query->where('mobile', 'like', "%{$searchTerm}%");
                        break;
                    case 'telephone':
                        $query->where('telephone', 'like', "%{$searchTerm}%");
                        break;
                    case 'national_id':
                        $query->where('national_id', 'like', "%{$searchTerm}%");
                        break;
                    case 'file_name':
                        $query->whereHas('files', function ($q) use ($searchTerm) {
                            $q->where('file_name', 'like', "%{$searchTerm}%");
                        });
                        break;
                    case 'land_no':
                        $query->whereHas('lands', function ($q) use ($searchTerm) {
                            $q->where('land_no', 'like', "%{$searchTerm}%");
                        });
                        break;
                }
            }

            // Barcode search
            if ($barcode) {
                $query->whereHas('files', function ($q) use ($barcode) {
                    $q->where('barcode', $barcode);
                });
            }

            // Non-Super Admin: only show results when searching or scanning barcode
            $hasSearchCriteria = $searchTerm || $barcode;
            $requiresSearch = true;

            if (!$hasSearchCriteria) {
                $clients = Client::whereRaw('1 = 0')->paginate(25)->withQueryString();
            } else {
                $clients = $query->orderBy('excel_row_number', 'asc')->paginate(25)->withQueryString();
            }

            $items = Item::orderBy('order')->get();

            return view('admin.clients.index', compact(
                'clients',
                'totalClients',
                'totalFiles',
                'totalPages',
                'searchTerm',
                'searchField',
                'barcode',
                'items',
                'requiresSearch'
            ));
        }

    }
}

