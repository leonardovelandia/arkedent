<?php

namespace Database\Seeders;

use App\Models\PlantillaConsentimiento;
use Illuminate\Database\Seeder;

class PlantillasConsentimientoSeeder extends Seeder
{
    public function run(): void
    {
        $plantillas = [
            [
                'nombre' => 'Extracción Dental Simple',
                'tipo'   => 'extraccion',
                'contenido' => 'CONSENTIMIENTO INFORMADO PARA EXTRACCIÓN DENTAL

Yo, {{nombre_paciente}} {{apellido_paciente}}, identificado(a) con documento {{documento_paciente}}, declaro que:

El Dr./Dra. {{doctor}} me ha explicado de forma clara y comprensible que requiero la extracción del/los diente(s) indicado(s), así como los siguientes aspectos:

PROCEDIMIENTO:
La extracción dental consiste en la remoción del diente de su alvéolo mediante el uso de anestesia local, fórceps y elevadores dentales. El procedimiento se realiza bajo los más estrictos protocolos de asepsia y bioseguridad.

RIESGOS Y COMPLICACIONES POSIBLES:
• Dolor e inflamación postoperatoria
• Sangrado prolongado
• Infección del alvéolo (alveolitis seca o húmeda)
• Lesión de nervios adyacentes (parestesia temporal o permanente)
• Fractura de raíz o hueso alveolar
• Comunicación oroantral (en molares superiores)
• Reacción adversa o alérgica a la anestesia
• Trismus (dificultad para abrir la boca)

CUIDADOS POSTOPERATORIOS:
• Morder el algodón firmemente por 30-45 minutos
• No escupir ni enjuagarse durante las primeras 24 horas
• Aplicar compresas de hielo externamente las primeras 24 horas (20 min sí / 20 min no)
• Mantener dieta blanda y alimentos fríos o tibios las primeras 24 horas
• No fumar ni consumir bebidas alcohólicas por mínimo 48 horas
• Tomar la medicación prescrita según las indicaciones dadas
• Ante sangrado abundante, fiebre o dolor intenso, comunicarse inmediatamente

Habiendo sido informado(a) completamente sobre el procedimiento, sus riesgos y alternativas, de forma libre, espontánea y voluntaria CONSIENTO en la realización del procedimiento de extracción dental.
',
            ],

            [
                'nombre' => 'Tratamiento de Conductos (Endodoncia)',
                'tipo'   => 'endodoncia',
                'contenido' => 'CONSENTIMIENTO INFORMADO PARA TRATAMIENTO DE CONDUCTOS (ENDODONCIA)

Yo, {{nombre_paciente}} {{apellido_paciente}}, identificado(a) con documento {{documento_paciente}}, declaro que:

El Dr./Dra. {{doctor}} me ha explicado que requiero tratamiento endodóntico (tratamiento de conductos radiculares) en el/los diente(s) indicado(s), y comprendo lo siguiente:

PROCEDIMIENTO:
La endodoncia es el tratamiento mediante el cual se elimina la pulpa dental (nervio) infectada o inflamada, se limpian, conforman y desinfectan los conductos radiculares, y finalmente se sellan con un material biocompatible. El objetivo es salvar el diente natural evitando su extracción.

NÚMERO DE SESIONES:
El tratamiento puede requerir entre 1 y 3 sesiones dependiendo del grado de infección y la complejidad del caso. Se realizará bajo anestesia local en cada sesión.

RIESGOS Y COMPLICACIONES POSIBLES:
• Dolor e inflamación postoperatoria (esperada y normal)
• Fractura de instrumentos endodónticos dentro del conducto
• Perforación radicular o de furca
• Fracaso del tratamiento que requiera retratamiento o extracción
• Reabsorción radicular
• Fractura del diente tratado (razón por la cual se recomienda colocar corona)
• Inflamación persistente (absceso periapical)

POSTERIOR AL TRATAMIENTO:
• Se recomienda colocar una corona dental para proteger el diente tratado
• El diente tratado puede volverse más frágil con el tiempo
• Se requieren controles radiográficos periódicos

CUIDADOS POSTOPERATORIOS:
• Tomar la medicación prescrita puntualmente
• No morder con el diente tratado hasta colocar la restauración definitiva
• Consultar inmediatamente ante dolor severo, inflamación o fiebre

Habiendo sido informado(a) de todo lo anterior, de forma libre y voluntaria CONSIENTO en la realización del tratamiento endodóntico.
',
            ],

            [
                'nombre' => 'Tratamiento de Ortodoncia',
                'tipo'   => 'ortodoncia',
                'contenido' => 'CONSENTIMIENTO INFORMADO PARA TRATAMIENTO DE ORTODONCIA

Yo, {{nombre_paciente}} {{apellido_paciente}}, identificado(a) con documento {{documento_paciente}}, declaro que:

El Dr./Dra. {{doctor}} me ha explicado en detalle el plan de tratamiento de ortodoncia propuesto, y comprendo lo siguiente:

PROCEDIMIENTO:
El tratamiento de ortodoncia tiene como objetivo corregir la posición de los dientes y la relación entre los maxilares, mejorando la función masticatoria, la estética dental y la salud bucal en general.

DURACIÓN ESTIMADA DEL TRATAMIENTO:
El tratamiento tiene una duración estimada de 18 a 36 meses, pudiendo variar según la complejidad del caso, la colaboración del paciente y la biología individual de cada organismo.

TIPOS DE APARATOLOGÍA:
• Brackets metálicos convencionales
• Brackets estéticos (cerámicos o de zafiro)
• Alineadores transparentes (según indicación)
• Aparatos funcionales u ortopédicos (según necesidad)

RIESGOS Y POSIBLES COMPLICACIONES:
• Reabsorción radicular (acortamiento de raíces)
• Caries dental por acumulación de placa alrededor de los brackets
• Descalcificación del esmalte dental
• Enfermedad periodontal si no se mantiene una higiene adecuada
• Recidiva (reaparición de la maloclusión) sin uso de retenedores
• Lesiones en encías y mucosa por fricción de los aparatos
• Dolor e incomodidad especialmente en los primeros días y tras cada ajuste

COMPROMISOS DEL PACIENTE:
• Asistir puntualmente a las citas de control programadas
• Mantener una higiene oral impecable con los métodos indicados
• Usar los elementos auxiliares prescritos (elásticos, retenedores, etc.)
• Evitar alimentos duros, pegajosos o que puedan dañar los brackets
• Usar los retenedores indefinidamente al finalizar el tratamiento activo

Habiendo sido informado(a) completamente, de forma libre y voluntaria CONSIENTO en la realización del tratamiento de ortodoncia.
',
            ],

            [
                'nombre' => 'Colocación de Implante Dental',
                'tipo'   => 'implante',
                'contenido' => 'CONSENTIMIENTO INFORMADO PARA COLOCACIÓN DE IMPLANTE DENTAL

Yo, {{nombre_paciente}} {{apellido_paciente}}, identificado(a) con documento {{documento_paciente}}, declaro que:

El Dr./Dra. {{doctor}} me ha explicado el procedimiento de implantología dental y comprendo lo siguiente:

PROCEDIMIENTO:
Un implante dental es una raíz artificial de titanio que se coloca quirúrgicamente en el hueso maxilar o mandibular para reemplazar la raíz del diente perdido. El tratamiento se divide en fases:

FASE 1 - COLOCACIÓN DEL IMPLANTE:
Procedimiento quirúrgico bajo anestesia local en el que se inserta el implante de titanio en el hueso.

FASE 2 - OSEOINTEGRACIÓN:
Período de espera de 3 a 6 meses durante el cual el implante se fusiona con el hueso (oseointegración). Durante este tiempo se puede colocar una prótesis provisional.

FASE 3 - RESTAURACIÓN PROTÉSICA:
Colocación de la corona definitiva sobre el implante una vez confirmada la oseointegración.

TIEMPO TOTAL DEL TRATAMIENTO: 4 a 8 meses aproximadamente.

REQUISITOS PARA EL ÉXITO:
• Cantidad y calidad ósea suficiente (puede requerir injerto óseo previo)
• Buena salud general y buenas condiciones periodontales
• No padecer enfermedades que afecten la cicatrización (diabetes no controlada, etc.)
• No fumar (el tabaco reduce significativamente la tasa de éxito)

RIESGOS Y POSIBLES COMPLICACIONES:
• Fracaso de la oseointegración (rechazo del implante)
• Infección periimplantaria (periimplantitis)
• Lesión de nervios o vasos sanguíneos adyacentes
• Perforación del seno maxilar (en implantes superiores)
• Fractura del implante
• Necesidad de injerto óseo adicional

CUIDADOS POSTOPERATORIOS:
• Reposo relativo las primeras 24-48 horas
• Aplicar hielo localmente las primeras horas
• Dieta blanda por 2 semanas
• No fumar durante todo el período de oseointegración
• Higiene oral cuidadosa con los instrumentos indicados
• Controles periódicos obligatorios

Habiendo sido informado(a) de todo lo anterior, de forma libre y voluntaria CONSIENTO en la realización del procedimiento de implantología dental.
',
            ],

            [
                'nombre' => 'Blanqueamiento Dental',
                'tipo'   => 'blanqueamiento',
                'contenido' => 'CONSENTIMIENTO INFORMADO PARA BLANQUEAMIENTO DENTAL

Yo, {{nombre_paciente}} {{apellido_paciente}}, identificado(a) con documento {{documento_paciente}}, declaro que:

El Dr./Dra. {{doctor}} me ha explicado el procedimiento de blanqueamiento dental y comprendo lo siguiente:

PROCEDIMIENTO:
El blanqueamiento dental es un procedimiento estético que utiliza agentes blanqueadores (peróxido de hidrógeno o peróxido de carbamida) para aclarar el tono de los dientes. Puede realizarse en clínica (blanqueamiento en consulta) o con férulas personalizadas para uso en casa.

RESULTADOS ESPERADOS:
Los resultados varían según el tipo de mancha, el tono inicial de los dientes y la respuesta individual. Se puede esperar un aclaramiento de entre 2 y 8 tonos. Los resultados no son permanentes y pueden requerir sesiones de mantenimiento.

DURACIÓN DE LOS RESULTADOS: 6 meses a 2 años según hábitos alimentarios y de higiene.

CONTRAINDICACIONES:
• Embarazo y lactancia
• Menores de 18 años (esmalte en formación)
• Presencia de caries activas o enfermedad periodontal no tratada
• Dientes con restauraciones extensas en zonas visibles
• Hipersensibilidad dental severa preexistente
• Alergias conocidas a los componentes del gel blanqueador

EFECTOS SECUNDARIOS POSIBLES:
• Sensibilidad dental temporal durante y después del tratamiento
• Irritación de encías y mucosas si el gel entra en contacto con ellas
• Sensibilidad al frío, calor o alimentos ácidos (generalmente temporal)
• No blanquea restauraciones existentes (coronas, carillas, resinas)

INSTRUCCIONES POST-TRATAMIENTO:
• Evitar alimentos y bebidas con pigmentos las primeras 48 horas (café, té, vino tinto, etc.)
• No fumar durante las primeras 48 horas
• Usar pasta dental para dientes sensibles si hay molestias
• Mantener una higiene oral óptima para prolongar los resultados

Habiendo sido informado(a) completamente, de forma libre y voluntaria CONSIENTO en la realización del blanqueamiento dental.
',
            ],

            [
                'nombre' => 'Cirugía Periodontal',
                'tipo'   => 'periodoncia',
                'contenido' => 'CONSENTIMIENTO INFORMADO PARA CIRUGÍA PERIODONTAL

Yo, {{nombre_paciente}} {{apellido_paciente}}, identificado(a) con documento {{documento_paciente}}, declaro que:

El Dr./Dra. {{doctor}} me ha explicado el procedimiento de cirugía periodontal y comprendo lo siguiente:

DIAGNÓSTICO:
Presento enfermedad periodontal (periodontitis) que no ha respondido suficientemente al tratamiento periodontal no quirúrgico, por lo cual se requiere intervención quirúrgica para acceder a las superficies radiculares profundas y restaurar la salud de los tejidos de soporte dental.

PROCEDIMIENTO:
La cirugía periodontal puede incluir:
• Raspado y alisado radicular profundo bajo anestesia local
• Cirugía de colgajo (Widman modificado): incisión de la encía para acceder directamente a las raíces y el hueso
• Procedimientos regenerativos: injerto óseo o membranas de regeneración tisular guiada
• Alargamiento coronario

OBJETIVO: Eliminar las bolsas periodontales, detener la progresión de la enfermedad y, en casos favorables, regenerar el hueso perdido.

RIESGOS Y POSIBLES COMPLICACIONES:
• Dolor, inflamación y sangrado postoperatorio
• Recesión gingival (encía que "baja") con exposición de raíces
• Sensibilidad dental temporal o permanente por exposición radicular
• Infección postoperatoria
• Pérdida de papila interdental (triángulo negro)
• Fracaso en la regeneración ósea
• Recidiva de la enfermedad sin mantenimiento periodontal adecuado

CUIDADOS POSTOPERATORIOS:
• Tomar la medicación prescrita estrictamente
• Aplicar hielo las primeras 24-48 horas
• Dieta blanda las primeras dos semanas
• No cepillar la zona operada hasta indicación del profesional
• Enjuagues con clorhexidina según indicación
• Asistir a todos los controles postoperatorios programados
• Mantener el programa de mantenimiento periodontal de por vida

Habiendo sido informado(a) de todo lo anterior, de forma libre y voluntaria CONSIENTO en la realización de la cirugía periodontal.
',
            ],
        ];

        foreach ($plantillas as $datos) {
            PlantillaConsentimiento::updateOrCreate(
                ['nombre' => $datos['nombre'], 'tipo' => $datos['tipo']],
                ['contenido' => $datos['contenido'], 'activo' => true]
            );
        }

        $this->command->info('✓ Plantillas de consentimiento creadas: ' . count($plantillas));
    }
}
