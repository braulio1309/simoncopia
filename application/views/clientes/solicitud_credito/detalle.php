<?php 
if (isset($datos['id'])) {
    $solicitud = $this->clientes_model->obtener("clientes_solicitudes_credito", ["id" => $datos['id']]);
    $solicitud_detalle = $this->clientes_model->obtener("clientes_solicitudes_credito_detalle", ['cscd.solicitud_id' => $datos['id']]);
    echo "<input type='hidden' id='solicitud_credito_id' value='$solicitud->id' />";
}
?>

<?php if(!isset($solicitud)) { ?>
    <div class="block-header block-header--has-breadcrumb block-header--has-title">
        <div class="container">
            <div class="block-header__body">
                <h1 class="block-header__title">Solicitud de crédito</h1>
            </div>
        </div>
    </div>
<?php } ?>

<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <?php if(!isset($solicitud)) { ?>
                <center>
                    <iframe width="560" height="315" class="mt-2" src="https://www.youtube.com/embed/UzmWpRfq398?si=FmqbgPq4NSTTN3xT" title="YouTube" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </center>
            <?php } ?>
            
            <div class="card-body card-body--padding--1">
                <div class="form-row mb-2">
                    <div class="form-group col-md-7 col-sm-12 m-3" style="border: 1px solid #EBEBEB;">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="solicitud_nueva" id="solicitud_nueva1" value="1" <?php if(isset($solicitud) && $solicitud->nueva == 1) echo "checked"; ?>>
                                    <label class="form-check-label" for="solicitud_nueva1">
                                        Quiero crear una solicitud nueva *
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="solicitud_nueva" id="solicitud_nueva0" value="0" <?php if(isset($solicitud) && $solicitud->nueva == 0) echo "checked"; ?>>
                                    <label class="form-check-label" for="solicitud_nueva0">
                                        Quiero actualizar mi solicitud *
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-4 col-sm-12 m-3" style="border: 1px solid #EBEBEB;">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="solicitud_tiene_rut" id="solicitud_tiene_rut1" value="1" <?php if(isset($solicitud) && $solicitud->rut == 1) echo "checked"; ?>>
                                    <label class="form-check-label" for="solicitud_tiene_rut1">
                                        Tengo RUT *
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="solicitud_tiene_rut" id="solicitud_tiene_rut2" value="0" <?php if(isset($solicitud) && $solicitud->rut == 0) echo "checked"; ?>>
                                    <label class="form-check-label" for="solicitud_tiene_rut2">
                                        NO Tengo RUT *
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-divider"></div>

                <div class="tag-badge tag-badge--new badge_formulario mb-2 mt-2">
                    Datos básicos del solicitante
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="solicitud_persona_tipo">¿Eres persona natural o jurídica? *</label>
                        <select id="solicitud_persona_tipo" class="form-control">
                            <option value="">Selecciona...</option>
                            <option value="1">Persona natural</option>
                            <option value="2">Persona jurídica</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 persona_natural">
                        <label for="solicitud_nombre">Nombres *</label>
                        <input type="text" class="form-control" id="solicitud_nombre" value="<?php if(isset($solicitud)) echo $solicitud->nombre; ?>">
                    </div>

                    <div class="form-group col-md-2 persona_natural">
                        <label for="solicitud_primer_apellido">Primer apellido *</label>
                        <input type="text" class="form-control" id="solicitud_primer_apellido" value="<?php if(isset($solicitud)) echo $solicitud->primer_apellido; ?>">
                    </div>

                    <div class="form-group col-md-2 persona_natural">
                        <label for="solicitud_segundo_apellido">Segundo apellido</label>
                        <input type="text" class="form-control" id="solicitud_segundo_apellido" value="<?php if(isset($solicitud)) echo $solicitud->segundo_apellido; ?>">
                    </div>

                    <div class="form-group col-md-12 persona_juridica">
                        <label for="solicitud_razon_social">Razón social *</label>
                        <input type="text" class="form-control" id="solicitud_razon_social" disabled value="<?php if(isset($solicitud)) echo $solicitud->razon_social; ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-3">
                        <br>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="solicitud_tipo_documento" id="solicitud_tipo_documento1" value="1" <?php if(isset($solicitud) && $solicitud->identificacion_tipo_id == 4) echo "checked"; ?> autocomplete="off">
                            <label class="form-check-label" for="solicitud_tipo_documento1">
                                Cédula de ciudadanía *
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <br>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="solicitud_tipo_documento" id="solicitud_tipo_documento2" value="2" <?php if(isset($solicitud) && $solicitud->identificacion_tipo_id == 1) echo "checked"; ?> autocomplete="off">
                            <label class="form-check-label" for="solicitud_tipo_documento2">
                                Nit *
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="solicitud_numero_documento">Número de documento (Sin dígito de verificación) *</label>
                        <input type="text" class="form-control" id="solicitud_numero_documento" value="<?php if(isset($solicitud)) echo $solicitud->documento_numero; ?>" placeholder="Ej: 81100500231">
                    </div>

                    <div class="form-group col-md-2">
                        <label for="solicitud_fecha_expedicion">Fecha de expedición *</label>
                        <input type="date" class="form-control" id="solicitud_fecha_expedicion" value="<?php if(isset($solicitud)) echo $solicitud->fecha_expedicion; ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <div class="form-group">
                            <label for="solicitud_direccion">Dirección *</label>
                            <input type="text" class="form-control" id="solicitud_direccion" value="<?php if(isset($solicitud)) echo $solicitud->direccion; ?>">
                        </div>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="solicitud_departamento">Departamento *</label>
                        <select id="solicitud_departamento" class="form-control"></select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="solicitud_municipio">Municipio *</label>
                        <select id="solicitud_municipio" class="form-control"></select>
                    </div>

                    <div class="form-group col-md-2">
                        <label for="solicitud_telefono">Teléfono *</label>
                        <input type="text" class="form-control" id="solicitud_telefono" value="<?php if(isset($solicitud)) echo $solicitud->telefono; ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="solicitud_email">E-mail *</label>
                        <input type="text" class="form-control" id="solicitud_email" value="<?php if(isset($solicitud)) echo $solicitud->email; ?>">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="solicitud_correo_facturacion_electronica">Correo para facturación electrónica *</label>
                        <input type="text" class="form-control" id="solicitud_correo_facturacion_electronica" value="<?php if(isset($solicitud)) echo $solicitud->email_factura_electronica; ?>">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="solicitud_cantidad_vehiculos">Si posees vehículos propios, indica cuántos</label>
                        <input type="number" class="form-control" id="solicitud_cantidad_vehiculos" value="<?php if(isset($solicitud)) echo $solicitud->cantidad_vehiculos; ?>">
                    </div>
                </div>
                <div class="tag-badge tag-badge--new badge_formulario mb-2 mt-2">
                    Datos representante legal
                </div>
                <div class="form-row datos_persona_juridica">
                    <div class="form-group col-md-6">
                        <label for="solicitud_representante_legal">Nombre del representante legal *</label>
                        <input type="text" class="form-control" id="solicitud_representante_legal" value="<?php if(isset($solicitud)) echo $solicitud->representante_legal; ?>">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="solicitud_representante_legal_documento">Número de documento del representante legal *</label>
                        <input type="text" class="form-control" id="solicitud_representante_legal_documento" value="<?php if(isset($solicitud)) echo $solicitud->representante_legal_documento_numero; ?>">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="solicitud_representante_legal_correo">E-mail del representante legal</label>
                        <input type="text" class="form-control" id="solicitud_representante_legal_correo" value="<?php if(isset($solicitud)) echo $solicitud->representante_legal_correo; ?>">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="solicitud_celular">Celular del titular o Representante legal *</label>
                        <input type="text" class="form-control" id="solicitud_celular" value="<?php if(isset($solicitud)) echo $solicitud->celular; ?>">
                    </div>
                </div>
            </div>

            <div class="card-divider"></div>
            <div class="card-body card-body--padding--1">
                <div class="tag-badge tag-badge--new badge_formulario mb-2">
                    Personas de contacto
                </div>
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <h5>Tesorería y pagos (opcional)</h5>
                    </div>

                    <div class="form-group col-md-3">
                        <input type="text" class="form-control" id="tesoreria_nombre" placeholder="Nombre" value="<?php if(isset($solicitud)) echo $solicitud->tesoreria_nombre; ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <input type="text" class="form-control" id="tesoreria_email" placeholder="Correo electrónico" value="<?php if(isset($solicitud)) echo $solicitud->tesoreria_email; ?>">
                    </div>

                    <div class="form-group col-md-2">
                        <input type="text" class="form-control" id="tesoreria_telefono" placeholder="Teléfono directo" value="<?php if(isset($solicitud)) echo $solicitud->tesoreria_telefono; ?>">
                    </div>
                    <div class="form-group col-md-2">
                        <input type="text" class="form-control" id="tesoreria_celular" placeholder="Celular" value="<?php if(isset($solicitud)) echo $solicitud->tesoreria_celular; ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <h5>Comercial (opcional)</h5>
                    </div>

                    <div class="form-group col-md-3">
                        <input type="text" class="form-control" id="comercial_nombre" placeholder="Nombre" value="<?php if(isset($solicitud)) echo $solicitud->comercial_nombre; ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <input type="text" class="form-control" id="comercial_email" placeholder="Correo electrónico" value="<?php if(isset($solicitud)) echo $solicitud->comercial_email; ?>">
                    </div>

                    <div class="form-group col-md-2">
                        <input type="text" class="form-control" id="comercial_telefono" placeholder="Teléfono directo" value="<?php if(isset($solicitud)) echo $solicitud->comercial_telefono; ?>">
                    </div>
                    <div class="form-group col-md-2">
                        <input type="text" class="form-control" id="comercial_celular" placeholder="Celular" value="<?php if(isset($solicitud)) echo $solicitud->comercial_celular; ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <h5>Contabilidad (opcional)</h5>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="text" class="form-control" id="contabilidad_nombre" placeholder="Nombre" value="<?php if(isset($solicitud)) echo $solicitud->contabilidad_nombre; ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <input type="text" class="form-control" id="contabilidad_email" placeholder="Correo electrónico" value="<?php if(isset($solicitud)) echo $solicitud->contabilidad_email; ?>">
                    </div>

                    <div class="form-group col-md-2">
                        <input type="text" class="form-control" id="contabilidad_telefono" placeholder="Teléfono directo" value="<?php if(isset($solicitud)) echo $solicitud->contabilidad_telefono; ?>">
                    </div>
                    <div class="form-group col-md-2">
                        <input type="text" class="form-control" id="contabilidad_celular" placeholder="Celular" value="<?php if(isset($solicitud)) echo $solicitud->contabilidad_celular; ?>">
                    </div>
                </div>
            </div>

            <div class="card-divider"></div>
            <div class="card-body card-body--padding--1">
                <div class="tag-badge tag-badge--new badge_formulario mb-2">
                    Referencias comerciales
                </div>
                <div class="form-row">
                    <div class="form-group col-md-1">
                        <h5>1</h5>
                    </div>

                    <div class="form-group col-md-3">
                        <input type="text" class="form-control" id="referencia_comercial_entidad1" placeholder="Nombre de la entidad" value="<?php if(isset($solicitud)) echo $solicitud->referencia_comercial_entidad1; ?>">
                    </div>

                    <div class="form-group col-md-4">
                        <input type="text" class="form-control" id="referencia_comercial_celular1" placeholder="Celular" value="<?php if(isset($solicitud)) echo $solicitud->referencia_comercial_cel1; ?>">
                    </div>

                    <div class="form-group col-md-4">
                        <input type="text" class="form-control" id="referencia_comercial_direccion1" placeholder="Dirección" value="<?php if(isset($solicitud)) echo $solicitud->referencia_comercial_direccion1; ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-1">
                        <h5>2</h5>
                    </div>

                    <div class="form-group col-md-3">
                        <input type="text" class="form-control" id="referencia_comercial_entidad2" placeholder="Nombre de la entidad" value="<?php if(isset($solicitud)) echo $solicitud->referencia_comercial_entidad2; ?>">
                    </div>

                    <div class="form-group col-md-4">
                        <input type="text" class="form-control" id="referencia_comercial_celular2" placeholder="Celular" value="<?php if(isset($solicitud)) echo $solicitud->referencia_comercial_cel2; ?>">
                    </div>

                    <div class="form-group col-md-4">
                        <input type="text" class="form-control" id="referencia_comercial_direccion2" placeholder="Dirección" value="<?php if(isset($solicitud)) echo $solicitud->referencia_comercial_direccion2; ?>">
                    </div>
                </div>
            </div>

            <div class="card-divider"></div>
            <div class="card-body card-body--padding--1" id="personas_autorizadas">
                <div class="tag-badge tag-badge--new badge_formulario mb-2">
                    Personas autorizadas
                </div>
                <small>(personas a quiénes autorizas para brindar información de tu cuenta)</small>

                <?php
                $agregar = 1;

                if (isset($solicitud)) {
                    $detalle_persona_autorizada = array_values(array_filter($solicitud_detalle, function ($registro) {
                        return $registro->formulario_tipo == 3;
                    }));

                    if (!empty($detalle_persona_autorizada)) $agregar = count($detalle_persona_autorizada);
                }

                for($i = 0; $i < $agregar; $i++) { ?>
                    <div class="form-row" id="personas_autorizadas_<?php echo $i; ?>">
                        <div class="form-group col-md-4">
                            <label>Nombre</label>
                            <input type="text" class="form-control" id="personas_autorizadas_nombre_<?php echo $i; ?>" value="<?php if (isset($detalle_persona_autorizada[$i])) echo $detalle_persona_autorizada[$i]->nombre; ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Numero de identificación</label>
                            <input type="text" class="form-control" id="personas_autorizadas_identificacion_<?php echo $i; ?>" value="<?php if (isset($detalle_persona_autorizada[$i])) echo $detalle_persona_autorizada[$i]->documento_numero; ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Celular</label>
                            <input type="text" class="form-control" id="personas_autorizadas_celular_<?php echo $i; ?>" value="<?php if (isset($detalle_persona_autorizada[$i])) echo $detalle_persona_autorizada[$i]->celular; ?>">
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="form-group mx-3">
                <button class="btn btn-success" onClick="javascript:agregarCamposPersonaAutorizada('personas_autorizadas')">Agregar</button>
            </div>

            <div class="card-divider"></div>
            <div class="card-body card-body--padding--1 datos_persona_juridica" id="clientes">
                <div class="tag-badge tag-badge--new badge_formulario mb-2">
                    SAGRILAFT - Formulario de conocimiento de clientes
                </div>

                <div class="card-header">
                    <h5>Socios y/o accionistas (solo para personas jurídicas)</h5>
                </div>

                <?php
                $agregar = 1;

                if (isset($solicitud)) {
                    $detalle_socios = array_values(array_filter($solicitud_detalle, function ($registro) {
                        return $registro->formulario_tipo == 1;
                    }));

                    if (!empty($detalle_socios)) $agregar = count($detalle_socios);
                }

                for($i = 0; $i < $agregar; $i++) { ?>
                    <div class="form-row" id="clientes_<?php echo $i; ?>">
                        <div class="form-group col-md-3">
                            <label>Nombre socio o accionista</label>
                            <input type="text" class="form-control" id="clientes_nombre_<?php echo $i; ?>" value="<?php if (isset($detalle_socios[$i])) echo $detalle_socios[$i]->nombre; ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Tipo de identificación</label>
                            <select id="clientes_tipo_identificacion_<?php echo $i; ?>" class="form-control">
                                <option value="">Selecciona...</option>
                                <option value="C" data-tipo_tercero="1">Cédula de ciudadanía</option>
                                <option value="N" data-tipo_tercero="2">NIT</option>
                                <option value="E" data-tipo_tercero="1">Cédula de extranjería</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Número de identificación</label>
                            <input type="text" class="form-control" id="clientes_numero_documento_<?php echo $i; ?>" value="<?php if (isset($detalle_socios[$i])) echo $detalle_socios[$i]->documento_numero; ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>% participación</label>
                            <input type="text" class="form-control" id="clientes_porcentaje_participacion_<?php echo $i; ?>" value="<?php if (isset($detalle_socios[$i])) echo $detalle_socios[$i]->porcentaje_participacion; ?>">
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="form-group mx-3 datos_persona_juridica">
                <button class="btn btn-success" onClick="javascript:agregarCamposClientesSociosAccionistas('clientes')">Agregar</button>
            </div>

            <div class="card-divider"></div>
            <div class="card-body card-body--padding--1 datos_persona_juridica" id="beneficiarios_cliente">
                <div class="tag-badge tag-badge--new badge_formulario mb-2">
                    Beneficiarios finales de socios y/o accionistas iguales o superiores al 5%
                </div>
                <small>(Solo para personas jurídicas)</small>

                <?php
                $agregar = 1;

                if (isset($solicitud)) {
                    $detalle_beneficiarios = array_values(array_filter($solicitud_detalle, function ($registro) {
                        return $registro->formulario_tipo == 2;
                    }));

                    if (!empty($detalle_beneficiarios)) $agregar = count($detalle_beneficiarios);
                }

                for($i = 0; $i < $agregar; $i++) { ?>
                    <div class="form-row" id="beneficiarios_cliente_<?php echo $i; ?>">
                        <div class="form-group col-md-3">
                            <label>Nombre socio o accionista</label>
                            <input type="text" class="form-control" id="beneficiarios_cliente_nombre_<?php echo $i; ?>" value="<?php if (isset($detalle_beneficiarios[$i])) echo $detalle_beneficiarios[$i]->nombre; ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Tipo de identificación</label>
                            <select id="beneficiarios_cliente_tipo_identificacion_<?php echo $i; ?>" class="form-control">
                                <option value="">Selecciona...</option>
                                <option value="C" data-tipo_tercero="1">Cédula de ciudadanía</option>
                                <option value="N" data-tipo_tercero="2">NIT</option>
                                <option value="E" data-tipo_tercero="1">Cédula de extranjería</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Número de identificación</label>
                            <input type="text" class="form-control" id="beneficiarios_cliente_numero_documento_<?php echo $i; ?>" value="<?php if (isset($detalle_beneficiarios[$i])) echo $detalle_beneficiarios[$i]->documento_numero; ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>% participación</label>
                            <input type="text" class="form-control" id="beneficiarios_cliente_porcentaje_participacion_<?php echo $i; ?>" value="<?php if (isset($detalle_beneficiarios[$i])) echo $detalle_beneficiarios[$i]->porcentaje_participacion; ?>">
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="form-group mx-3 datos_persona_juridica">
                <button class="btn btn-success" onClick="javascript:agregarCamposClientesSociosAccionistas('beneficiarios_cliente')">Agregar</button>
            </div>

            <div class="card-divider"></div>
            <div class="card-body card-body--padding--1">
                <div class="tag-badge tag-badge--new badge_formulario mb-2">
                    Persona Políticamente Expuesta (PEP)
                </div>
                <div class="form-row">
                    <div class="col-md-6">
                        <label>¿Por su cargo o actividad maneja recursos públicos? *</label>
                    </div>

                    <div class="form-group col-md-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="recursos_publicos" id="recursos_publicos_si" value="1" <?php if(isset($solicitud) && $solicitud->recursos_publicos == 1) echo "checked"; ?>>
                            <label class="form-check-label" for="recursos_publicos_si">
                                Si
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="recursos_publicos" id="recursos_publicos_no" value="0" <?php if(isset($solicitud) && $solicitud->recursos_publicos == 0) echo "checked"; ?>>
                            <label class="form-check-label" for="recursos_publicos_no">
                                No
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <input type="text" class="form-control" id="recursos_publicos_cliente" placeholder="¿Cuál/quién?" value="<?php if(isset($solicitud)) echo $solicitud->recursos_publicos_cual; ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6">
                        <label>¿Por su cargo o actividad ejerce algún grado de poder público? *</label>
                    </div>

                    <div class="form-group col-md-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="poder_publico" id="poder_publico_si" <?php if(isset($solicitud) && $solicitud->poder_publico == 1) echo "checked"; ?>>
                            <label class="form-check-label" for="poder_publico_si">
                                Si
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="poder_publico" id="poder_publico_no" <?php if(isset($solicitud) && $solicitud->poder_publico == 0) echo "checked"; ?>>
                            <label class="form-check-label" for="poder_publico_no">
                                No
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <input type="text" class="form-control" id="poder_publico_cliente" placeholder="¿Cuál/quién?" value="<?php if(isset($solicitud)) echo $solicitud->poder_publico_cual; ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6">
                        <label>Por su actividad u oficio, goza usted de reconocimiento público *</label>
                    </div>

                    <div class="form-group col-md-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reconocimiento_publico" id="reconocimiento_publico_si" <?php if(isset($solicitud) && $solicitud->reconocimiento_publico == 1) echo "checked"; ?>>
                            <label class="form-check-label" for="reconocimiento_publico_si">
                                Si
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reconocimiento_publico" id="reconocimiento_publico_no" <?php if(isset($solicitud) && $solicitud->reconocimiento_publico == 0) echo "checked"; ?>>
                            <label class="form-check-label" for="reconocimiento_publico_no">
                                No
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <input type="text" class="form-control" id="reconocimiento_publico_cliente" placeholder="¿Cuál/quién?" value="<?php if(isset($solicitud)) echo $solicitud->reconocimiento_publico_cual; ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6">
                        <label>Existe algún vínculo entre usted y una persona considerada expuesta *</label>
                    </div>

                    <div class="form-group col-md-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="persona_expuesta" id="persona_expuesta_si" <?php if(isset($solicitud) && $solicitud->persona_expuesta == 1) echo "checked"; ?>>
                            <label class="form-check-label" for="persona_expuesta_si">
                                Si
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="persona_expuesta" id="persona_expuesta_no" <?php if(isset($solicitud) && $solicitud->persona_expuesta == 0) echo "checked"; ?>>
                            <label class="form-check-label" for="persona_expuesta_no">
                                No
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <input type="text" class="form-control" id="persona_expuesta_cliente" placeholder="¿Cuál/quién?" value="<?php if(isset($solicitud)) echo $solicitud->persona_expuesta_cual; ?>">
                    </div>
                </div>
            </div>

            <div class="card-divider"></div>
            <div class="card-body card-body--padding--1">
                <div class="tag-badge tag-badge--new badge_formulario mb-2">
                    Información financiera (en pesos)
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="solicitud_ingresos_mensuales">Ingresos mensuales (pesos)</label>
                        <input type="number" class="form-control" id="solicitud_ingresos_mensuales" placeholder="$0" value="<?php if(isset($solicitud)) echo $solicitud->ingresos_mensuales; ?>">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="solicitud_egresos_mensuales">Egresos mensuales (Gastos)</label>
                        <input type="number" class="form-control" id="solicitud_egresos_mensuales" placeholder="$0" value="<?php if(isset($solicitud)) echo $solicitud->egresos_mensuales; ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="solicitud_activos">Activos (Pertenencias. Ej: propiedades, vehículos, etc.)</label>
                        <input type="number" class="form-control" id="solicitud_activos" placeholder="$0" value="<?php if(isset($solicitud)) echo $solicitud->activos; ?>">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="solicitud_pasivos">Pasivos (Deudas)</label>
                        <input type="number" class="form-control" id="solicitud_pasivos" placeholder="$0" value="<?php if(isset($solicitud)) echo $solicitud->pasivos; ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="solicitud_otros_ingresos">Otros ingresos (pesos)</label>
                        <input type="number" class="form-control" id="solicitud_otros_ingresos" placeholder="$0" value="<?php if(isset($solicitud)) echo $solicitud->otros_ingresos; ?>">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="solicitud_concepto_otros_ingresos">Concepto otros ingresos</label>
                        <input type="text" class="form-control" id="solicitud_concepto_otros_ingresos" placeholder="Describa el origen de los otros ingresos" value="<?php if(isset($solicitud)) echo $solicitud->concepto_otros_ingresos; ?>">
                    </div>
                </div>
            </div>

            <div class="card-body card-body--padding--1">
                <!-- Si es una solicitud nueva -->
                <?php  if (!isset($datos['id'])) { ?>
                    <div class="tag-badge tag-badge--new badge_formulario mb-2">
                        Documentos requeridos
                    </div>
                    <div class="container">
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th rowspan="2">Documentos</th>
                                </tr>
                                <tr>
                                    <th>Archivo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="documentos_juridica documentos_natural">
                                    <td>Fotocopia cédula del Representante Legal *</td>
                                    <td class="text-center">
                                        <input type="file" class="form-control archivos" id="fotocopia_cedula">
                                    </td>
                                </tr>
                                <tr class="documentos_juridica documentos_natural">
                                    <td>Fotocopia RUT *</td>
                                    <td class="text-center">
                                        <input type="file" class="form-control archivos" id="fotocopia_rut">
                                    </td>
                                </tr>
                                <tr class="documentos_juridica d-none">
                                    <td>Fotocopia Cámara de Comercio (no mayor a 30 días) *</td>
                                    <td class="text-center">
                                        <input type="file" class="form-control archivos" id="fotocopia_camara_comercio">
                                    </td>
                                </tr>
                                <tr class="documentos_juridica documentos_natural">
                                    <td>Selfie con el documento de identidad (Opcional)</td>
                                    <td class="text-center">
                                        <input type="file" class="form-control archivos" id="selfie_documento">
                                    </td>
                                </tr>
                                <tr class="documentos_juridica d-none">
                                    <td>Extractos bancarios últimos 3 meses (Opcional)</td>
                                    <td class="text-center">
                                        <input type="file" class="form-control archivos" id="extractos_bancarios">
                                    </td>
                                </tr>
                                <tr class="documentos_juridica d-none">
                                    <td>2 referencias comerciales (Opcional)</td>
                                    <td class="text-center">
                                        <input type="file" class="form-control archivos" id="referencias_comerciales" multiple>
                                    </td>
                                </tr>
                                <tr class="documentos_juridica d-none">
                                    <td>Estados financieros año anterior (Opcional)</td>
                                    <td class="text-center">
                                        <input type="file" class="form-control archivos" id="estados_financieros">
                                    </td>
                                </tr>
                                <tr class="documentos_juridica documentos_natural">
                                    <td>Declaración de renta año anterior (opcional)</td>
                                    <td class="text-center">
                                        <input type="file" class="form-control archivos" id="declaracion_renta">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>

                <div class="form-row">
                    <div class="form-group col-12">
                        <label for="vendedor_id">Elige tu asesor comercial *</label>
                        <select id="vendedor_id" class="form-control">
                            <option value="">Sin asesor comercial asignado</option>
                            <?php foreach($this->configuracion_model->obtener('vendedores') as $vendedor) echo "<option value='$vendedor->id'>$vendedor->nombre</option>"; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row mt-2 alert alert-primary" role="alert">
                    <label class="form-check-label col-md-12 mt-2" for="solicitud_preferencia_enlace1">
                        Deseo recibir el enlace para firmar la solicitud por:
                    </label>

                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="solicitud_preferencia_enlace" id="solicitud_preferencia_enlace1" value="1" <?php if(isset($solicitud) && $solicitud->preferencia_enlace == 1) echo "checked"; ?>>
                            <label class="form-check-label" for="solicitud_preferencia_enlace1">
                                WhatsApp
                            </label>
                        </div>
                    </div>
                
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="solicitud_preferencia_enlace" id="solicitud_preferencia_enlace2" value="2" <?php if(isset($solicitud) && $solicitud->preferencia_enlace == 2) echo "checked"; ?>>
                            <label class="form-check-label" for="solicitud_preferencia_enlace2">
                                Correo electrónico
                            </label>
                        </div>
                    </div>
                    <hr>

                    <label class="form-check-label mt-2" for="solicitud_preferencia_enlace1">
                        <ul>
                            <li>RECUERDA QUE TE LLEGARÁ UN MENSAJE DEL PROVEEDOR <b>HOMINI BIOMETRIC</b></li>
                            <li>DESPUÉS DE ENVIADA LA NOTIFICACIÓN DE FIRMA ELECTRÓNICA, CUENTAS CON <b>24 HORAS</b> PARA REALIZARLA.</li>
                        </ul>
                    </label>
                </div>
            </div>

            <div class="form-group mb-2 mt-2 mx-3 my-2">
                <?php if(!isset($solicitud)) { ?>
                    <button class="btn btn-primary btn-block" onClick="javascript:crearSolicitudCredito()" id="btn_enviar_solicitud">ENVIAR SOLICITUD DE CRÉDITO</button>
                    
                    <div class="address-card__row mt-2 mb-2">
                        <div class="alert alert-light mb-3">
                            Si tienes alguna inquietud o presentas inconvenientes, por favor comunícate al 604 444 7232 (extensiones 105 - 110 - 111) o a los celulares 310 297 8620 - 311 272 8428
                        </div>
                    </div>
                <?php } else { ?>
                    <button class="btn btn-primary btn-block mb-3" onClick="javascript:crearSolicitudCredito()" id="btn_enviar_solicitud">ACTUALIZAR DATOS DE LA SOLICITUD</button>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script>
    agregarCamposClientesSociosAccionistas = (elemento) => {
        let index = $(`#${elemento} .form-row`).length

        $(`#${elemento}`).append(`
            <hr>
            <div class="form-row" id="${elemento}_${index}">
                <div class="form-group col-md-3">
                    <label>Nombre socio o accionista</label>
                    <input type="text" class="form-control" id="${elemento}_nombre_${index}">
                </div>

                <div class="form-group col-md-3">
                    <label>Tipo de identificación</label>
                    <select id="${elemento}_tipo_identificacion_${index}" class="form-control">
                        <option value="">Selecciona...</option>
                        <option value="C" data-tipo_tercero="1">Cédula de ciudadanía</option>
                        <option value="N" data-tipo_tercero="2">NIT</option>
                        <option value="E" data-tipo_tercero="1">Cédula de extranjería</option>
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label>Número de identificación</label>
                    <input type="text" class="form-control" id="${elemento}_numero_documento_${index}">
                </div>

                <div class="form-group col-md-3">
                    <label>% participación</label>
                    <input type="text" class="form-control" id="${elemento}_porcentaje_participacion_${index}">
                </div>
            </div>
        `)
    }

    agregarCamposPersonaAutorizada = (elemento) => {
        let index = $(`#${elemento} .form-row`).length

        $(`#${elemento}`).append(`
            <hr>
            <div class="form-row" id="${elemento}_${index}">
                <div class="form-group col-md-4">
                    <label>Nombre</label>
                    <input type="text" class="form-control" id="${elemento}_nombre_${index}">
                </div>

                <div class="form-group col-md-4">
                    <label>Numero de identificación</label>
                    <input type="text" class="form-control" id="${elemento}_identificacion_${index}">
                </div>

                <div class="form-group col-md-4">
                    <label>Celular</label>
                    <input type="text" class="form-control" id="${elemento}_celular_${index}">
                </div>
            </div>
        `)
    }

    obtenerClientesSociosAccionistas = (elemento, solicitudId = null) => {
        let registros = []

        $(`div[id^='${elemento}_']`).each(function(index) {
            let valores = 0

            let registro = {
                nombre: $(`#${elemento}_nombre_${index}`).val(),
                identificacion_tipo_id: $(`#${elemento}_tipo_identificacion_${index} option:selected`).attr('data-tipo_tercero'),
                documento_numero: $(`#${elemento}_numero_documento_${index}`).val(),
                porcentaje_participacion: $(`#${elemento}_porcentaje_participacion_${index}`).val()
            }

            Object.values(registro).forEach(valor => { if(valor) valores+=1 })
            if (valores > 0) {
                registro.solicitud_id = solicitudId
                registro.formulario_tipo = (elemento === 'clientes') ? 1: 2
                registros.push(registro)
            }
        })

        return registros
    }

    obtenerCamposPersonasAutorizadas = (elemento, solicitudId) => {
        let registros = []

        $(`div[id^='${elemento}_']`).each(function(index) {
            let valores = 0

            let registro = {
                nombre: $(`#${elemento}_nombre_${index}`).val(),
                documento_numero: $(`#${elemento}_identificacion_${index}`).val(),
                celular: $(`#${elemento}_celular_${index}`).val(),
            }

            Object.values(registro).forEach(valor => { if(valor) valores+=1 })
            if (valores > 0) {
                registro.solicitud_id = solicitudId
                registro.formulario_tipo = 3
                registros.push(registro)
            }
        })

        return registros
    }

    concatenarRazonSocial = () => {
        $('#solicitud_razon_social').val(`${$('#solicitud_primer_apellido').val()} ${$('#solicitud_segundo_apellido').val()} ${$('#solicitud_nombre').val()}`)
    }

    crearSolicitudCredito = async() => {
        let camposObligatorios = [
            $('#solicitud_persona_tipo'),
            $('#solicitud_razon_social'),
            $('#solicitud_numero_documento'),
            $('#solicitud_direccion'),
            $('#solicitud_email'),
            $('#solicitud_correo_facturacion_electronica'),
            $('#solicitud_municipio'),
            $('#solicitud_telefono'),
            $('#solicitud_departamento'),
            $('#solicitud_fecha_expedicion'),
            $('#vendedor_id'),
        ]

        // Si es persona natural, incluir campos obligatorios
        if (!$('#solicitud_persona_tipo').val() || $('#solicitud_persona_tipo').val() == 1) {
            camposObligatorios.push(
                $('#solicitud_nombre'),
                $('#solicitud_primer_apellido'),
            )
        }

        let camposRadioObligatorios = [
            'solicitud_nueva',
            'solicitud_tiene_rut',
            'solicitud_tipo_documento',
            'recursos_publicos',
            'reconocimiento_publico',
            'persona_expuesta',
            'poder_publico',
            'solicitud_preferencia_enlace',
        ]

        // Si es persona jurídica
        if ($('#solicitud_persona_tipo').val() == 2) {
            camposObligatorios.push($('#solicitud_representante_legal'), $('#solicitud_celular'))
            camposObligatorios.push($('#solicitud_representante_legal_documento'))
        }

        if (!validarCamposObligatorios(camposObligatorios)) return false
        if (!validarCamposTipoRadio(camposRadioObligatorios)) return false

        if ($('#solicitud_persona_tipo').val() == 2 && obtenerClientesSociosAccionistas('clientes').length < 1) {
            mostrarAviso('alerta', `Por favor diligencia la sección de socios y/o accionistas`, 20000)
            return false
        }

        let archivos = validarArchivos()
        if (!archivos) {
            mostrarAviso('alerta', `Por favor selecciona los archivos para poder finalizar la solicitud de crédito`, 20000)
            return false
        }

        let datosSolicitud = {
            tipo: 'clientes_solicitudes_credito',
            nombre: $('#solicitud_nombre').val().toUpperCase(),
            primer_apellido: $('#solicitud_primer_apellido').val().toUpperCase(),
            segundo_apellido: $('#solicitud_segundo_apellido').val().toUpperCase(),
            razon_social: $('#solicitud_razon_social').val().toUpperCase(),
            persona_tipo_id: $('#solicitud_persona_tipo').val(),
            identificacion_tipo_id: ($(`#solicitud_tipo_documento1`).is(':checked')) ? 4 : 1, // 4: CC, 1: NIT
            documento_numero: $('#solicitud_numero_documento').val(),
            departamento_id: $('#solicitud_departamento').val(),
            ciudad_id: $('#solicitud_municipio').val(),
            direccion: $('#solicitud_direccion').val(),
            telefono: $('#solicitud_telefono').val(),
            email: $('#solicitud_email').val(),
            celular: $('#solicitud_celular').val(),
            representante_legal: $('#solicitud_representante_legal').val(),
            representante_legal_documento_numero: $('#solicitud_representante_legal_documento').val(),
            representante_legal_correo: $('#solicitud_representante_legal_correo').val(),
            email_factura_electronica: $('#solicitud_correo_facturacion_electronica').val(),
            tesoreria_nombre: $('#tesoreria_nombre').val(),
            tesoreria_email: $('#tesoreria_email').val(),
            tesoreria_telefono: $('#tesoreria_telefono').val(),
            tesoreria_celular: $('#tesoreria_celular').val(),
            comercial_nombre: $('#comercial_nombre').val(),
            comercial_email: $('#comercial_email').val(),
            comercial_telefono: $('#comercial_telefono').val(),
            comercial_celular: $('#comercial_celular').val(),
            contabilidad_nombre: $('#contabilidad_nombre').val(),
            contabilidad_email: $('#contabilidad_email').val(),
            contabilidad_telefono: $('#contabilidad_telefono').val(),
            contabilidad_celular: $('#contabilidad_celular').val(),
            referencia_comercial_entidad1: $('#referencia_comercial_entidad1').val(),
            referencia_comercial_cel1: $('#referencia_comercial_celular1').val(),
            referencia_comercial_direccion1: $('#referencia_comercial_direccion1').val(),
            referencia_comercial_entidad2: $('#referencia_comercial_entidad2').val(),
            referencia_comercial_cel2: $('#referencia_comercial_celular2').val(),
            referencia_comercial_direccion2: $('#referencia_comercial_direccion2').val(),
            reconocimiento_publico: ($(`#reconocimiento_publico_si`).is(':checked')) ? 1 : 0,
            reconocimiento_publico_cual: $('#reconocimiento_publico_cliente').val(),
            rut: ($(`#solicitud_tiene_rut1`).is(':checked')) ? 1 : 0,
            persona_expuesta: ($(`#persona_expuesta_si`).is(':checked')) ? 1: 0,
            persona_expuesta_cual: $('#persona_expuesta_cliente').val(),
            poder_publico: ($(`#poder_publico_si`).is(':checked')) ? 1: 0,
            poder_publico_cual: $('#poder_publico_cliente').val(),
            recursos_publicos: ($(`#recursos_publicos_si`).is(':checked')) ? 1: 0,
            recursos_publicos_cual: $('#recursos_publicos_cliente').val(),
            ingresos_mensuales: $('#solicitud_ingresos_mensuales').val(),
            egresos_mensuales: $('#solicitud_egresos_mensuales').val(),
            activos: $('#solicitud_activos').val(),
            pasivos: $('#solicitud_pasivos').val(),
            otros_ingresos: $('#solicitud_otros_ingresos').val(),
            concepto_otros_ingresos: $('#solicitud_concepto_otros_ingresos').val(),
            nueva: ($(`#solicitud_nueva1`).is(':checked')) ? 1: 0,
            preferencia_enlace: ($(`#solicitud_preferencia_enlace1`).is(':checked')) ? 1: 2,
            fecha_expedicion: $('#solicitud_fecha_expedicion').val(),
            tercero_vendedor_id: $('#vendedor_id').val(),
            cantidad_vehiculos: $("#solicitud_cantidad_vehiculos").val(),
            token: generarToken(),  // Token para que la solicitud sea única
        }

        $('#btn_enviar_solicitud').prop("disabled", true)

        // Si es un usuario logueado en el sistema, se agrega el id
        if($('#sesion_usuario_id').val()) datosSolicitud.usuario_id = $('#sesion_usuario_id').val()

        Swal.fire({
            title: 'Estamos creando la solicitud de crédito en nuestros sistemas...',
            text: 'Por favor, espera.',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })

        if(!$('#solicitud_credito_id').val()) {
            // Se crea la solicitud de crédito
            let solicitudId = await consulta('crear', datosSolicitud, false)
            solicitudCreditoId = solicitudId.resultado

            mensaje = `¡Tu solicitud de crédito ha sido creada correctamente! Te enviaremos un correo electrónico de confirmación y nos comunicaremos contigo lo más pronto posible.`
        } else {
            datosSolicitud.id = $('#solicitud_credito_id').val()
            solicitudCreditoId = datosSolicitud.id

            mensaje = 'Solicitud actualizada correctamente'
            
            await consulta('actualizar', datosSolicitud)
        }

        // Se crean los terceros asociados
        let personasAutorizadas = obtenerCamposPersonasAutorizadas("personas_autorizadas", solicitudCreditoId)
        let sociosAccionistas = obtenerClientesSociosAccionistas('clientes', solicitudCreditoId)
        let beneficicariosSociosAccionistas = obtenerClientesSociosAccionistas('beneficiarios_cliente', solicitudCreditoId)

        if (personasAutorizadas.length > 0) consulta('crear', {tipo: "clientes_solicitudes_credito_detalle", valores: personasAutorizadas, id_solicitud_credito: solicitudCreditoId}, false)
        if (sociosAccionistas.length > 0) consulta('crear', {tipo: "clientes_solicitudes_credito_detalle", valores: sociosAccionistas}, false)
        if (beneficicariosSociosAccionistas.length > 0) consulta('crear', {tipo: "clientes_solicitudes_credito_detalle", valores: beneficicariosSociosAccionistas}, false)

        // Creación del registro en bitácora
        var datosBitacora = {
            tipo: 'clientes_solicitudes_credito_bitacora',
            solicitud_id: solicitudCreditoId,
            usuario_id: $('#sesion_usuario_id').val(),
            observaciones: `Solicitud recibida`,
        }
        consulta('crear', datosBitacora, false)

        await subirArchivos(solicitudCreditoId, archivos)
        Swal.close()

        mostrarAviso("exito", mensaje, 30000)
    }

    subirArchivos = async (solicitudCreditoId, archivos) => {
        let cantidadArchivos = archivos.length
        let cantidadArchivosSubidos = 0

        Array.from(archivos).forEach((archivo, index) => {
            let documento = new FormData()

            documento.append(index, archivo)

            let subida = new XMLHttpRequest()
            subida.open('POST', `${$("#site_url").val()}clientes/subir/${solicitudCreditoId}`)
            subida.send(documento)
            subida.onload = async evento => {
                let respuesta = JSON.parse(evento.target.responseText)

                cantidadArchivosSubidos+=1

                // Si se subieron todos los archivos se genera el reporte y envía el email
                if (cantidadArchivos === cantidadArchivosSubidos) {
                    let creacionPDF = await obtenerPromesa(`${$("#site_url").val()}reportes/pdf/solicitud_credito/${solicitudCreditoId}`)
                    // console.log('creacionPDF', creacionPDF)

                    // Se envía un correo electrónico de notificación
                    let email = await obtenerPromesa(`${$('#site_url').val()}interfaces/enviar_email`, {tipo: 'solicitud_credito', id: solicitudCreditoId})
                    // console.log('Email', email)
                }
            }
        })
    }

    validarArchivos = () => {
        let reglas = {
            fotocopia_cedula: 1,
            fotocopia_rut: 1,
            fotocopia_camara_comercio: 1,
            selfie_documento: 0,
            extractos_bancarios: 0,
            referencias_comerciales: 0,
            estados_financieros: 0,
            declaracion_renta: 0,
        }

        let archivosSubir = []
        let cumpleReglas = true

        $(`tr:not(.d-none) .archivos`).each(function(index, elemento) {
            let $elemento = $(elemento)
            let archivos = $elemento.prop('files')
            let numeroPorRegla = reglas[$(elemento).attr('id')]

            if (!numeroPorRegla || numeroPorRegla === archivos.length) {
                let archivo =  Array.from(archivos)
                archivosSubir = archivosSubir.concat(archivo)

                $elemento.removeClass("is-invalid")
            } else {
                $elemento.addClass("is-invalid")
                cumpleReglas = false
            }
        })

        return (cumpleReglas) ? archivosSubir: cumpleReglas
    }

    $().ready(async () => {
        // Cuando se seleccione el tipo de persona
        $('#solicitud_persona_tipo').change(() => {
            if(!$('#solicitud_tiene_rut1').is(':checked')) $('#solicitud_tipo_documento1, #solicitud_tipo_documento2').attr('disabled', false)

            // Si es persona natural
            if ($('#solicitud_persona_tipo').val() == 1) {
                $('.documentos_juridica, .datos_persona_juridica').addClass('d-none')
                $('.persona_natural, .documentos_natural').removeClass('d-none')
                $('.persona_juridica').removeClass('col-md-8').addClass('col-md-12')
                $('.persona_juridica input').attr('disabled', true)
                $('#solicitud_razon_social').val('')
                concatenarRazonSocial()
            }

            // Si es persona jurídica
            if ($('#solicitud_persona_tipo').val() == 2) {
                $('.persona_natural').addClass('d-none')
                $('.documentos_juridica, .datos_persona_juridica').removeClass('d-none')
                $('.persona_juridica').removeClass('col-md-12').addClass('col-md-8')
                $('.persona_juridica input').attr('disabled', false)

                $('#solicitud_tipo_documento2').prop('checked', true)
                $('#solicitud_tipo_documento1, #solicitud_tipo_documento2').attr('disabled', true)
            }
        })

        $('.persona_natural input').keyup(() => {
            // Si es persona natural
            if ($('#solicitud_persona_tipo').val() == 1) concatenarRazonSocial()
        })

        // Cuando se seleccione si tiene RUT o no
        $('input[name="solicitud_tiene_rut"]').change(() => {
            // Si tiene RUT
            if ($('#solicitud_tiene_rut1').is(':checked')) {
                // Se bloquean los tipos de documentos
                $("#solicitud_tipo_documento1, #solicitud_tipo_documento2").attr('disabled', true)

                // Se marca checkeado el Nit
                $("#solicitud_tipo_documento2").attr('checked', true)
            } else {
                // Se desbloquean los tipos de documentos
                $("#solicitud_tipo_documento1, #solicitud_tipo_documento2").attr('disabled', false).attr('checked', false)
            }
        })

        listarDatos('solicitud_departamento', {tipo: 'departamentos', pais_id: 169})

        $('#solicitud_departamento').change(() => {
            listarDatos('solicitud_municipio', {tipo: 'municipios', departamento_id: $('#solicitud_departamento').val()})
        })

        // Control del input para que registren solamente números
        $(`#solicitud_numero_documento`).keyup(function() {
            $(this).val(limpiarCadena($(this).val()))
        })
    })
</script>

<?php if(isset($solicitud)) { ?>
    <script>
        $().ready(async () => {
            $("#solicitud_persona_tipo").val(<?php echo $solicitud->persona_tipo_id ?>).trigger("change")
            $("#vendedor_id").val(<?php echo $solicitud->tercero_vendedor_id ?>)
            await listarDatos('solicitud_departamento', {tipo: 'departamentos', pais_id: 169})
            $("#solicitud_departamento").val('<?php echo $solicitud->departamento_codigo ?>')
            await listarDatos('solicitud_municipio', {tipo: 'municipios', departamento_id: $('#solicitud_departamento').val()})
            $("#solicitud_municipio").val('<?php echo $solicitud->municipio_codigo ?>')
        })
    </script>
<?php } ?>