<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="persona_id" class="col-sm-6 control-label">Persona</label>
            <div class="col-sm-10">
                <select id="persona_id" class="form-control">
                    <option value="">-- Selecciona una persona</option>
                </select>
            </div>
        </div>
    </div>
    @if(auth()->user()->hasRole('Super Usuario'))
        <div class="col-md-6">
            <div class="form-group">
                <label for="centro_id" class="col-sm-6 control-label">Centro de conciliación</label>
                <div class="col-sm-10">
                    <select id="centro_id" class="form-control">
                        <option value="">-- Selecciona un centro</option>
                    </select>
                </div>
            </div>
        </div>
    @else
    {!! Form::hidden('centro_id',auth()->user()->centro_id ,[]) !!}
    @endif
</div>