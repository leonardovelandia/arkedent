<?php
namespace App\Http\Controllers;

use App\Models\ControlPeriodontal;
use App\Models\FichaPeriodontal;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ControlPeriodontalController extends Controller
{
    public function create(Request $request, $ficha)
    {
        $ficha           = FichaPeriodontal::with('paciente', 'controles')->where('activo', true)->findOrFail($ficha);
        $doctores        = \App\Models\User::orderBy('name')->get();
        $siguienteSesion = $ficha->controles()->count() + 1;

        return view('periodoncia.controles.create', compact('ficha', 'doctores', 'siguienteSesion'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ficha_periodontal_id' => 'required|exists:fichas_periodontales,id',
            'paciente_id'          => 'required|exists:pacientes,id',
            'user_id'              => 'required|exists:users,id',
            'fecha_control'        => 'required|date',
            'tipo_sesion'          => 'required',
            'numero_sesion'        => 'required|integer',
        ]);

        $data = $request->only([
            'ficha_periodontal_id','paciente_id','user_id',
            'fecha_control','numero_sesion','tipo_sesion',
            'indice_placa_control','indice_gingival_control',
            'anestesia_utilizada','instrumentos_utilizados',
            'observaciones','indicaciones_paciente','proxima_cita_semanas',
        ]);

        if ($request->filled('zonas_tratadas')) {
            $data['zonas_tratadas'] = $request->zonas_tratadas;
        }
        if ($request->filled('sondaje_control')) {
            $data['sondaje_control'] = json_decode($request->sondaje_control, true);
        }

        $control = ControlPeriodontal::create($data);

        // Auto-cambiar estado de ficha a en_tratamiento si estaba activa
        $ficha = FichaPeriodontal::find($request->ficha_periodontal_id);
        if ($ficha && $ficha->estado === 'activa') {
            $ficha->update(['estado' => 'en_tratamiento']);
        }

        return redirect()->route('periodoncia.show', $request->ficha_periodontal_id)
            ->with('exito', 'Control periodontal registrado exitosamente.');
    }

    public function show($id)
    {
        $control = ControlPeriodontal::with(['fichaPeriodontal.paciente', 'periodoncista'])->findOrFail($id);
        return view('periodoncia.controles.show', compact('control'));
    }

    public function edit($id)
    {
        $control  = ControlPeriodontal::with('fichaPeriodontal.paciente')->findOrFail($id);
        $doctores = \App\Models\User::orderBy('name')->get();
        return view('periodoncia.controles.edit', compact('control', 'doctores'));
    }

    public function update(Request $request, $id)
    {
        $control = ControlPeriodontal::findOrFail($id);

        $request->validate([
            'fecha_control' => 'required|date',
            'tipo_sesion'   => 'required',
        ]);

        $data = $request->only([
            'user_id','fecha_control','tipo_sesion',
            'indice_placa_control','indice_gingival_control',
            'anestesia_utilizada','instrumentos_utilizados',
            'observaciones','indicaciones_paciente','proxima_cita_semanas',
        ]);

        if ($request->filled('zonas_tratadas')) {
            $data['zonas_tratadas'] = $request->zonas_tratadas;
        }
        if ($request->filled('sondaje_control')) {
            $data['sondaje_control'] = json_decode($request->sondaje_control, true);
        }

        $control->update($data);

        return redirect()->route('periodoncia.show', $control->ficha_periodontal_id)
            ->with('exito', 'Control periodontal actualizado.');
    }

    public function destroy($id)
    {
        $control = ControlPeriodontal::findOrFail($id);
        $fichaId = $control->ficha_periodontal_id;
        $control->delete();
        return redirect()->route('periodoncia.show', $fichaId)
            ->with('exito', 'Control periodontal eliminado.');
    }

    public function pdf($id)
    {
        $control = ControlPeriodontal::with(['fichaPeriodontal.paciente', 'periodoncista'])->findOrFail($id);
        $config  = Configuracion::obtener();
        $colorPDF = '#1E3A5F';

        $pdf = Pdf::loadView('periodoncia.controles.pdf', compact('control', 'config', 'colorPDF'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('control-periodontal-' . $control->numero_control . '.pdf');
    }
}
