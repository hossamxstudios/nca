<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\LandController;
use App\Http\Controllers\Admin\FileController;
use App\Http\Controllers\Admin\PhysicalLocationController;
use App\Http\Controllers\Admin\GeographicAreaController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\BackupController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [PageController::class, 'index'])->name('dashboard');
});

// Admin Routes
Route::middleware(['auth'])->name('admin.')->group(function () {
    // Clients
    Route::get('/clients'                                               , [ClientController::class,                      'index'])->name('clients.index');
    Route::get('/clients/export'                                        , [ClientController::class,                     'export'])->name('clients.export');
    Route::get('/clients/create'                                        , [ClientController::class,                     'create'])->name('clients.create');
    Route::get('/clients/trash'                                         , [ClientController::class,                      'trash'])->name('clients.trash');
    Route::post('/clients/bulk-delete'                                  , [ClientController::class,                 'bulkDelete'])->name('clients.bulk-delete');
    Route::post('/clients/bulk-print-data'                              , [ClientController::class,              'bulkPrintData'])->name('clients.bulk-print-data');
    Route::post('/clients/bulk-force-delete'                            , [ClientController::class,            'bulkForceDelete'])->name('clients.bulk-force-delete');
    Route::post('/clients/bulk-restore'                                 , [ClientController::class,                'bulkRestore'])->name('clients.bulk-restore');
    Route::post('/clients'                                              , [ClientController::class,                      'store'])->name('clients.store');
    Route::get('/clients/{client}'                                      , [ClientController::class,                       'show'])->name('clients.show');
    Route::get('/clients/{client}/edit'                                 , [ClientController::class,                       'edit'])->name('clients.edit');
    Route::put('/clients/{client}'                                      , [ClientController::class,                     'update'])->name('clients.update');
    Route::get('/clients/{client}/delete'                               , [ClientController::class,              'confirmDelete'])->name('clients.delete');
    Route::delete('/clients/{client}'                                   , [ClientController::class,                    'destroy'])->name('clients.destroy');
    Route::get('/clients/{client}/print-barcodes'                       , [ClientController::class,              'printBarcodes'])->name('clients.print-barcodes');
    Route::post('/clients/log-print'                                     , [ClientController::class,                   'logPrint'])->name('clients.log-print');
    Route::post('/clients/log-view'                                      , [ClientController::class,                    'logView'])->name('clients.log-view');
    Route::post('/clients/{id}/restore'                                 , [ClientController::class,                    'restore'])->name('clients.restore');
    Route::delete('/clients/{id}/force-delete'                          , [ClientController::class,                'forceDelete'])->name('clients.force-delete');
    // Files
    Route::post('/files/{file}/upload'                                  , [FileController::class,                       'upload'])->name('files.upload');
    Route::get('/files/{file}/download-pages'                           , [FileController::class,                'downloadPages'])->name('files.download-pages');
    Route::get('/files/{file}/download-original'                        , [FileController::class,             'downloadOriginal'])->name('files.download-original');
    Route::put('/files/{file}/update-items'                             , [FileController::class,                  'updateItems'])->name('files.update-items');
    Route::post('/files/{file}/combine'                                 , [FileController::class,                 'combineFiles'])->name('files.combine');
    Route::put('/files/{file}/update-location'                          , [FileController::class,               'updateLocation'])->name('files.update-location');
    Route::post('/clients/{client}/files'                               , [FileController::class,                        'store'])->name('files.store');
    // API for cascading dropdowns
    Route::get('/api/districts'                                         , [FileController::class,                 'getDistricts'])->name('api.districts');
    Route::get('/api/zones/{district}'                                  , [FileController::class,                     'getZones'])->name('api.zones');
    Route::get('/api/areas/{zone}'                                      , [FileController::class,                     'getAreas'])->name('api.areas');
    Route::get('/api/rooms'                                             , [FileController::class,                     'getRooms'])->name('api.rooms');
    Route::get('/api/lanes/{room}'                                      , [FileController::class,                     'getLanes'])->name('api.lanes');
    Route::get('/api/stands/{lane}'                                     , [FileController::class,                    'getStands'])->name('api.stands');
    Route::get('/api/racks/{stand}'                                     , [FileController::class,                     'getRacks'])->name('api.racks');
    // Users
    Route::get('/users'                                                 , [UserController::class,                        'index'])->name('users.index');
    Route::post('/users'                                                , [UserController::class,                        'store'])->name('users.store');
    Route::post('/users/bulk-delete'                                    , [UserController::class,                   'bulkDelete'])->name('users.bulk-delete');
    Route::post('/users/bulk-restore'                                   , [UserController::class,                  'bulkRestore'])->name('users.bulk-restore');
    Route::post('/users/bulk-force-delete'                              , [UserController::class,              'bulkForceDelete'])->name('users.bulk-force-delete');
    Route::get('/users/{id}'                                            , [UserController::class,                         'show'])->name('users.show');
    Route::post('/users/{id}'                                           , [UserController::class,                       'update'])->name('users.update');
    Route::post('/users/{id}/destroy'                                   , [UserController::class,                      'destroy'])->name('users.destroy');
    Route::post('/users/{id}/restore'                                   , [UserController::class,                      'restore'])->name('users.restore');
    Route::post('/users/{id}/force-delete'                              , [UserController::class,                  'forceDelete'])->name('users.force-delete');
    Route::post('/users/{id}/toggle-status'                             , [UserController::class,                 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{id}/assign-role'                               , [UserController::class,                   'assignRole'])->name('users.assign-role');
    Route::post('/users/{id}/change-password'                           , [UserController::class,               'changePassword'])->name('users.change-password');
    // Roles & Permissions
    Route::get('/roles'                                                 , [RoleController::class,                        'index'])->name('roles.index');
    Route::get('/roles/create'                                          , [RoleController::class,                       'create'])->name('roles.create');
    Route::post('/roles'                                                , [RoleController::class,                        'store'])->name('roles.store');
    Route::post('/roles/bulk-delete'                                    , [RoleController::class,                   'bulkDelete'])->name('roles.bulk-delete');
    Route::get('/roles/{id}'                                            , [RoleController::class,                         'show'])->name('roles.show');
    Route::get('/roles/{id}/edit'                                       , [RoleController::class,                         'edit'])->name('roles.edit');
    Route::post('/roles/{id}'                                           , [RoleController::class,                       'update'])->name('roles.update');
    Route::post('/roles/{id}/destroy'                                   , [RoleController::class,                      'destroy'])->name('roles.destroy');
    Route::post('/roles/{id}/sync-permissions'                          , [RoleController::class,              'syncPermissions'])->name('roles.sync-permissions');
    // Geographic Areas
    Route::get('/geographic-areas'                                      , [GeographicAreaController::class,              'index'])->name('geographic-areas.index');
    Route::get('/geographic-areas/governorates/{governorate}/show'      , [GeographicAreaController::class,    'showGovernorate'])->name('geographic-areas.governorates.show');
    Route::post('/geographic-areas/governorates'                        , [GeographicAreaController::class,   'storeGovernorate'])->name('geographic-areas.governorates.store');
    Route::post('/geographic-areas/governorates/{governorate}'          , [GeographicAreaController::class,  'updateGovernorate'])->name('geographic-areas.governorates.update');
    Route::post('/geographic-areas/governorates/{governorate}/delete'   , [GeographicAreaController::class, 'destroyGovernorate'])->name('geographic-areas.governorates.destroy');
    Route::post('/geographic-areas/cities'                              , [GeographicAreaController::class,          'storeCity'])->name('geographic-areas.cities.store');
    Route::post('/geographic-areas/cities/{city}'                       , [GeographicAreaController::class,         'updateCity'])->name('geographic-areas.cities.update');
    Route::post('/geographic-areas/cities/{city}/delete'                , [GeographicAreaController::class,        'destroyCity'])->name('geographic-areas.cities.destroy');
    Route::post('/geographic-areas/districts'                           , [GeographicAreaController::class,      'storeDistrict'])->name('geographic-areas.districts.store');
    Route::post('/geographic-areas/districts/{district}'                , [GeographicAreaController::class,     'updateDistrict'])->name('geographic-areas.districts.update');
    Route::post('/geographic-areas/districts/{district}/delete'         , [GeographicAreaController::class,    'destroyDistrict'])->name('geographic-areas.districts.destroy');
    Route::post('/geographic-areas/zones'                               , [GeographicAreaController::class,          'storeZone'])->name('geographic-areas.zones.store');
    Route::post('/geographic-areas/zones/{zone}'                        , [GeographicAreaController::class,         'updateZone'])->name('geographic-areas.zones.update');
    Route::post('/geographic-areas/zones/{zone}/delete'                 , [GeographicAreaController::class,        'destroyZone'])->name('geographic-areas.zones.destroy');
    Route::post('/geographic-areas/areas'                               , [GeographicAreaController::class,          'storeArea'])->name('geographic-areas.areas.store');
    Route::post('/geographic-areas/areas/{area}'                        , [GeographicAreaController::class,         'updateArea'])->name('geographic-areas.areas.update');
    Route::post('/geographic-areas/areas/{area}/delete'                 , [GeographicAreaController::class,        'destroyArea'])->name('geographic-areas.areas.destroy');
    // Physical Locations
    Route::get('/physical-locations'                                    , [PhysicalLocationController::class,            'index'])->name('physical-locations.index');
    Route::get('/physical-locations/rooms/{room}/show'                  , [PhysicalLocationController::class,         'showRoom'])->name('physical-locations.rooms.show');
    Route::post('/physical-locations/rooms'                             , [PhysicalLocationController::class,        'storeRoom'])->name('physical-locations.rooms.store');
    Route::post('/physical-locations/rooms/{room}'                      , [PhysicalLocationController::class,       'updateRoom'])->name('physical-locations.rooms.update');
    Route::post('/physical-locations/rooms/{room}/delete'               , [PhysicalLocationController::class,      'destroyRoom'])->name('physical-locations.rooms.destroy');
    Route::post('/physical-locations/lanes'                             , [PhysicalLocationController::class,        'storeLane'])->name('physical-locations.lanes.store');
    Route::post('/physical-locations/lanes/{lane}'                      , [PhysicalLocationController::class,       'updateLane'])->name('physical-locations.lanes.update');
    Route::post('/physical-locations/lanes/{lane}/delete'               , [PhysicalLocationController::class,      'destroyLane'])->name('physical-locations.lanes.destroy');
    Route::post('/physical-locations/stands'                            , [PhysicalLocationController::class,       'storeStand'])->name('physical-locations.stands.store');
    Route::post('/physical-locations/stands/{stand}'                    , [PhysicalLocationController::class,      'updateStand'])->name('physical-locations.stands.update');
    Route::post('/physical-locations/stands/{stand}/delete'             , [PhysicalLocationController::class,     'destroyStand'])->name('physical-locations.stands.destroy');
    Route::post('/physical-locations/racks'                             , [PhysicalLocationController::class,          'storeRack'])->name('physical-locations.racks.store');
    Route::post('/physical-locations/racks/{rack}'                      , [PhysicalLocationController::class,         'updateRack'])->name('physical-locations.racks.update');
    Route::post('/physical-locations/racks/{rack}/delete'               , [PhysicalLocationController::class,        'destroyRack'])->name('physical-locations.racks.destroy');
    // Items (Content Types)
    Route::get('/items'                                                 , [ItemController::class,                        'index'])->name('items.index');
    Route::post('/items'                                                , [ItemController::class,                        'store'])->name('items.store');
    Route::post('/items/{item}'                                         , [ItemController::class,                       'update'])->name('items.update');
    Route::post('/items/{item}/delete'                                  , [ItemController::class,                      'destroy'])->name('items.destroy');
    Route::post('/items/update-order'                                   , [ItemController::class,                  'updateOrder'])->name('items.update-order');
    // Imports
    Route::get('/imports'                                               , [ImportController::class,                       'index'])->name('imports.index');
    Route::post('/imports'                                              , [ImportController::class,                       'store'])->name('imports.store');
    Route::get('/imports/{import}/show'                                 , [ImportController::class,                        'show'])->name('imports.show');
    Route::get('/imports/{import}/progress'                             , [ImportController::class,                    'progress'])->name('imports.progress');
    Route::post('/imports/{import}/delete'                              , [ImportController::class,                     'destroy'])->name('imports.destroy');
    Route::get('/imports/download-template'                             , [ImportController::class,            'downloadTemplate'])->name('imports.download-template');
    // Profile
    Route::get('/profile'                                               , [ProfileController::class,                       'index'])->name('profile.index');
    Route::post('/profile'                                              , [ProfileController::class,                      'update'])->name('profile.update');
    Route::post('/profile/password'                                     , [ProfileController::class,              'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar/remove'                                , [ProfileController::class,                'removeAvatar'])->name('profile.avatar.remove');
    // Activity Logs
    Route::middleware('can:activity-logs.view')->group(function () {
        Route::get('/activity-logs'                                     , [ActivityLogController::class,                    'index'])->name('activity-logs.index');
        Route::get('/activity-logs/user/{user}'                         , [ActivityLogController::class,             'userTimeline'])->name('activity-logs.user-timeline');
        Route::get('/activity-logs/{activityLog}'                       , [ActivityLogController::class,                     'show'])->name('activity-logs.show');
    });
    Route::middleware('can:activity-logs.delete')->group(function () {
        Route::post('/activity-logs/bulk-delete'                        , [ActivityLogController::class,               'bulkDelete'])->name('activity-logs.bulk-delete');
        Route::post('/activity-logs/clear-old'                          , [ActivityLogController::class,                 'clearOld'])->name('activity-logs.clear-old');
        Route::delete('/activity-logs/{activityLog}'                    , [ActivityLogController::class,                  'destroy'])->name('activity-logs.destroy');
    });
    // Backup
    Route::middleware('can:backup.access')->group(function () {
        Route::get('/backup'                                            , [BackupController::class,                        'index'])->name('backup.index');
        Route::get('/backup/download'                                   , [BackupController::class,                     'download'])->name('backup.download');
    });
});

Route::get('/test-error/{code}', function ($code) {
    abort($code);
});
// require __DIR__.'/auth.php';
