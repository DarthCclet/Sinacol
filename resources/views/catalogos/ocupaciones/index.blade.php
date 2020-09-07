@extends('layouts.default', ['paceTop' => true])

@section('title', 'Ocupaciones Laborales')

@include('includes.component.datatables')

@include('includes.component.pickers')

@section('content')

    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:;">Tables</a></li>
        <li class="breadcrumb-item active">Managed Tables</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administrar ocupaciones <small>Listado de ocupaciones</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Listado </h4>
            <div class="panel-heading-btn">
                <a href="{!! route('ocupaciones.create') !!}" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Nuevo</a>
            </div>
        </div>

        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            @include('catalogos.ocupaciones._list')
        </div>
    </div>

    <!-- inicio Modal de modificaciones multiples-->
    <div class="modal" id="modal-ocupaciones" aria-hidden="true" style="display:none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edición múltiple de ocupaciones</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <!-- <div class="col-md-3"></div> -->
                    <h6>Ocupaciones a editar:</h6>
                    <div id="divOcupaciones" class="col-md-8" style="border: 1px gray dotted; background: #d9d7d733; overflow: scroll; height: 80px; ">
                        <div id="table_ocupaciones">

                        </div>
                    </div>
                    <hr>
                    <div id="divEdicionOcupaciones col-md-12" >
                      <div class="col-md-12 row">
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label>Salario Zona Libre</label>
                                  <input type="text" id="salario_libre" class="form-control" placeholder="Salario minimo en zona libre"/>
                              </div>
                          </div>
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label>Salario Resto del Pais</label>
                                  <input type="text" id="salario_resto" class="form-control" placeholder="Salario minimo en resto del pais" />
                              </div>
                          </div>
                        </div>
                        <div class="col-md-12 row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha de inicio de vigencia</label>
                                    <input type="text" id="vigencia_de" class="form-control date" placeholder="Fecha de inicio de vigencia"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha de termino de vigencia</label>
                                    <input type="text" id="vigencia_a" class="form-control date" placeholder="Fecha de fin de vigencia"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="text-right">
                        <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-sign-out"></i> Cerrar</a>
                        <!-- <button class="btn btn-primary btn-sm m-l-5" id="btnRegresar"><i class="fa fa-arrow-left"></i> Regresar</button> -->
                        <button class="btn btn-primary btn-sm m-l-5" id="btnGuardarMultiple"><i class="fa fa-save"></i> Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin Modal de modificaciones multiples-->

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#data-table-default').DataTable({paging: false,"info":false,language: {url: "/assets/plugins/datatables.net/dataTable.es.json"}});
                //Confirm para eliminar
                $('.btn-borrar').on('click', function (e) {
                    let that = this;
                    e.preventDefault();
                    swal({
                        title: '¿Está seguro?',
                        text: 'Al oprimir el botón de aceptar se eliminará el registro',
                        icon: 'warning',
                        buttons: {
                            cancel: {
                                text: 'Cancelar',
                                value: null,
                                visible: true,
                                className: 'btn btn-default',
                                closeModal: true,
                            },
                            confirm: {
                                text: 'Aceptar',
                                value: true,
                                visible: true,
                                className: 'btn btn-warning',
                                closeModal: true
                            }
                        }
                    }).then(function(isConfirm){
                        if(isConfirm){
                            $(that).closest('form').submit();
                        }
                    });
                    return false;
                });

                //Mostrar boton de edicion multiple
                $('input[name="selectPuestos"]').click( function(){
                  countOcupaciones = 0;
                  $.each($("input[name='selectPuestos']:checked"), function(){
                    countOcupaciones ++;
                  });
                  if(countOcupaciones >=1){
                    $("#btnsMultiple").show();
                  }else{
                    $("#btnsMultiple").hide();
                  }
                });

                //Mostrar modal para edicion
                let ocupacionesSelec = new Array();
                $('#btmMultiple').on('click', function (e) {
                  ocupacionesSelec =[];
                  $("#modal-ocupaciones").modal("show");
                  let tabla ="";

                  tabla +='<ul>';
                  // tabla +='<table class="table table-striped table-bordered table-td-valign-middle" >';
                  // tabla +='<thead>';
                  // tabla +='<tr><th>Ocupaciones</th></tr>';
                  // tabla +='</thead>';
                  // tabla +='<tbody>';
                  $.each($("input[name='selectPuestos']:checked"), function(){
                    ocupacionesSelec.push( $(this).attr('ocid') );
                    // tabla +='<tr class="" ><td>'+ $(this).val() +'</td><tr>';
                    tabla +='<li>'+ $(this).val() +'</li>';
                  });
                  // tabla +='</tbody>';
                  // tabla +='</table>';
                  tabla +='</ul>';

                  $('#table_ocupaciones').html(tabla);
                  console.log(ocupacionesSelec);
                });

                // Guardar actualizacion de vigencia
                $("#btnGuardarMultiple").on("click",function(){
                  swal({
                      title: '¿Está seguro?',
                      text: 'Al oprimir el botón de aceptar se actualizarán todos los registros seleccionados',
                      icon: 'warning',
                      buttons: {
                          cancel: {
                              text: 'Cancelar',
                              value: null,
                              visible: true,
                              className: 'btn btn-default',
                              closeModal: true,
                          },
                          confirm: {
                              text: 'Aceptar',
                              value: true,
                              visible: true,
                              className: 'btn btn-warning',
                              closeModal: true
                          }
                      }
                  }).then(function(isConfirm){
                      if(isConfirm){
                        // let dataOcupaciones = new Array();
                        // dataOcupaciones.push({
                        //
                        // });
                        // console.log(dataOcupaciones);
                        $.ajax({
                            url:"/api/ocupacion/multiples",
                            type:"POST",
                            dataType:"json",
                            data:{
                                ids:ocupacionesSelec,
                                vigencia_de:$(vigencia_de).val(),
                                vigencia_a:$(vigencia_a).val(),
                                salario_zona_libre:$(salario_libre).val(),
                                salario_resto_del_pais:$(salario_resto).val()
                            },
                            success:function(data){
                        //         getCentroIncidencias($("#id").val());
                                // swal({
                        //             title: 'Éxito',
                        //             text: 'Se guardarón los datos de la disponibilidad',
                        //             icon: 'success'
                        //         });
                            }
                        });
                      }
                  });

                    // var validacion = validarCamposIncidencia();
                    // console.log(validacion);
                    // if(!validacion.error){
                        // $.ajax({
                        //     url:"/api/ocupaciones/editMultiple",
                        //     type:"POST",
                        //     dataType:"json",
                        //     data:dataOcupaciones,
                        //     success:function(data){
                        //         getCentroIncidencias($("#id").val());
                        //         swal({
                        //             title: 'Éxito',
                        //             text: 'Se guardarón los datos de la disponibilidad',
                        //             icon: 'success'
                        //         });
                        //     }
                        // });
                    // }else{
                    //     swal({
                    //         title: 'Algo salió mal',
                    //         text: validacion.msgError,
                    //         icon: 'warning'
                    //     });
                    // }
                });

                $('.date').datetimepicker({useCurrent: false,format:'DD/MM/YYYY'});

        });
    </script>
@endpush
