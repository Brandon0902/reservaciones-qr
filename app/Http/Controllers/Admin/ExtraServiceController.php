<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreExtraServiceRequest;
use App\Http\Requests\Admin\UpdateExtraServiceRequest;
use App\Models\ExtraService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExtraServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin.only']);
    }

    /** Listado + filtro */
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));

        $rows = ExtraService::query()
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.servicios_extras.index', compact('rows', 'q'));
    }

    /** Form de creación */
    public function create(): View
    {
        return view('admin.servicios_extras.create');
    }

    /** Guardar nuevo */
    public function store(StoreExtraServiceRequest $request): RedirectResponse
    {
        ExtraService::create($request->validated());

        return redirect()
            ->route('admin.extra-services.index')
            ->with('success', 'Servicio extra creado correctamente.');
    }

    /** Form de edición */
    public function edit(ExtraService $servicio): View
    {
        return view('admin.servicios_extras.edit', compact('servicio'));
    }

    /** Actualizar existente */
    public function update(UpdateExtraServiceRequest $request, ExtraService $servicio): RedirectResponse
    {
        $servicio->update($request->validated());

        return redirect()
            ->route('admin.extra-services.index')
            ->with('success', 'Servicio extra actualizado.');
    }

    /** Eliminar */
    public function destroy(ExtraService $servicio): RedirectResponse
    {
        $servicio->delete();

        return redirect()
            ->route('admin.extra-services.index')
            ->with('success', 'Servicio extra eliminado.');
    }
}
