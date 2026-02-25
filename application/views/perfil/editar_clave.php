<div class="card">
    <div class="card-header">
        <h5>Change Password</h5>
    </div>
    <div class="card-divider"></div>
    <div class="card-body card-body--padding--2">
        <div class="row no-gutters">
            <div class="col-12 col-lg-7 col-xl-6">
                <div class="form-group">
                    <label for="password-current">Current Password</label>
                    <input type="password" class="form-control" id="password-current" placeholder="Current Password">
                    <div id="reglas_clave_perfil" class="mt-2" style="display:none; font-size:0.85em;">
                        <div id="pc_regla_longitud" class="text-danger"><i class="fas fa-times-circle"></i> Mínimo 12 caracteres</div>
                        <div id="pc_regla_numero" class="text-danger"><i class="fas fa-times-circle"></i> Al menos un número</div>
                        <div id="pc_regla_mayuscula" class="text-danger"><i class="fas fa-times-circle"></i> Al menos una letra mayúscula</div>
                        <div id="pc_regla_minuscula" class="text-danger"><i class="fas fa-times-circle"></i> Al menos una letra minúscula</div>
                        <div id="pc_regla_especial" class="text-danger"><i class="fas fa-times-circle"></i> Al menos un carácter especial (!@#$%^&amp;*...)</div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password-new">New Password</label>
                    <input type="password" class="form-control" id="password-new" placeholder="New Password">
                </div>
                <div class="form-group">
                    <label for="password-confirm">Reenter New Password</label>
                    <input type="password" class="form-control" id="password-confirm" placeholder="Reenter New Password">
                </div>
                <div class="form-group mb-0">
                    <button class="btn btn-primary mt-3">Change</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $().ready(() => {
        $('#password-new').on('focus input', function() {
            $('#reglas_clave_perfil').show()
            const clave = $(this).val()
            const reglas = [
                { id: 'pc_regla_longitud',  ok: clave.length >= 12,          texto: 'Mínimo 12 caracteres' },
                { id: 'pc_regla_numero',    ok: /[0-9]/.test(clave),         texto: 'Al menos un número' },
                { id: 'pc_regla_mayuscula', ok: /[A-Z]/.test(clave),         texto: 'Al menos una letra mayúscula' },
                { id: 'pc_regla_minuscula', ok: /[a-z]/.test(clave),         texto: 'Al menos una letra minúscula' },
                { id: 'pc_regla_especial',  ok: /[^A-Za-z0-9]/.test(clave), texto: 'Al menos un carácter especial (!@#$%^&*...)' },
            ]
            reglas.forEach(r => {
                const icono = r.ok ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-times-circle"></i>'
                $(`#${r.id}`).html(`${icono} ${r.texto}`)
                    .removeClass('text-danger text-success')
                    .addClass(r.ok ? 'text-success' : 'text-danger')
            })
        }).on('blur', function() {
            if ($(this).val() === '') $('#reglas_clave_perfil').hide()
        })
    })
</script>