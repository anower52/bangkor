@extends('layouts.app')

@section('title','Slider')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">

@endpush

@section('content')
    <div class="container">
        <br />
        <h3 align="center">Slider</h3>
        <br />
        <div align="right">
            <button type="button" name="create_record" id="create_record" class="btn btn-success btn-sm">Create Record</button>
        </div>
        <br />
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="user_table">
                <thead>
                <tr>
                    <th width="10%">Image</th>
                    <th width="35%">Title</th>
                    <th width="35%">Sub Title</th>
                    <th width="35%">Description</th>
                    <th width="30%">Action</th>
                </tr>
                </thead>
            </table>
        </div>
        <br />
        <br />
    </div>


    <div id="formModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add New Record</h4>
                </div>
                <div class="modal-body">
                    <span id="form_result"></span>
                    <form method="post" id="sample_form" class="form-horizontal" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label class="control-label col-md-4" >Title : </label>
                            <div class="col-md-8">
                                <input type="text" name="title" id="title" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" >Sub Title : </label>
                            <div class="col-md-8">
                                <input type="text" name="sub_title" id="sub_title" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" >Description : </label>
                            <div class="col-md-8">
                                <input type="text" name="description" id="description" class="form-control" />
                            </div>
                        </div>
                        <div class="row">
                            <label class="control-label col-md-4">Select Profile Image : </label>
                            <div class="col-md-8">
                                <input type="file" name="image" id="image" />
                                <span id="store_image"></span>
                            </div>
                        </div>
                        <br />
                        <div class="form-group" align="center">
                            <input type="hidden" name="action" id="action" />
                            <input type="hidden" name="hidden_id" id="hidden_id" />
                            <input type="submit" name="action_button" id="action_button" class="btn btn-warning" value="Add" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection

@push('scripts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta.3/js/bootstrap.min.js"></script>

    {{--data table js start--}}
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#table').DataTable();
        } );
    </script>
{{--data table js end--}}

    <script>
        $(document).ready(function(){

            $('#user_table').DataTable({
                processing: true,
                serverSide: true,
                ajax:{
                    url: "{{ route('slider.index') }}",
                },
                columns:[
                    {
                        data: 'image',
                        name: 'image',
                        render: function(data, type, full, meta){
                            return "<img src={{ URL::to('/') }}/slider/" + data + " width='70' class='img-thumbnail' />";
                        },
                        orderable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'sub_title',
                        name: 'sub_title'
                    },

                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    }
                ]
            });

            $('#create_record').click(function(){
                $('.modal-title').text("Add New Record");
                $('#action_button').val("Add");
                $('#action').val("Add");
                $('#formModal').modal('show');
            });

            $('#sample_form').on('submit', function(event){
                event.preventDefault();
                if($('#action').val() == 'Add')
                {
                    $.ajax({
                        url:"{{ route('slider.store') }}",
                        method:"POST",
                        data: new FormData(this),
                        contentType: false,
                        cache:false,
                        processData: false,
                        dataType:"json",
                        success:function(data)
                        {
                            var html = '';
                            if(data.errors)
                            {
                                html = '<div class="alert alert-danger">';
                                for(var count = 0; count < data.errors.length; count++)
                                {
                                    html += '<p>' + data.errors[count] + '</p>';
                                }
                                html += '</div>';
                            }
                            if(data.success)
                            {
                                html = '<div class="alert alert-success">' + data.success + '</div>';
                                $('#sample_form')[0].reset();
                                $('#user_table').DataTable().ajax.reload();
                            }
                            $('#form_result').html(html);
                        }
                    })
                }

                if($('#action').val() == "Edit")
                {
                    $.ajax({
                        url:"allSlider/update",
                        method:"POST",
                        data:new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
                        dataType:"json",
                        success:function(data)
                        {
                            var html = '';
                            if(data.errors)
                            {
                                html = '<div class="alert alert-danger">';
                                for(var count = 0; count < data.errors.length; count++)
                                {
                                    html += '<p>' + data.errors[count] + '</p>';
                                }
                                html += '</div>';
                            }
                            if(data.success)
                            {
                                html = '<div class="alert alert-success">' + data.success + '</div>';
                                $('#sample_form')[0].reset();
                                $('#store_image').html('');
                                $('#user_table').DataTable().ajax.reload();

                            }
                            $('#form_result').html(html);
                        }
                    });
                }

            });

            $(document).on('click', '.edit', function(){
                var id = $(this).attr('id');
                $('#form_result').html('');
                $.ajax({
                    url:"/bangkorpulp/public/admin/slider/"+id+"/edit",
                    dataType:"json",
                    success:function(html){
                        $('#title').val(html.data.title);
                        $('#sub_title').val(html.data.sub_title);
                        $('#description').val(html.data.description);
                        $('#store_image').html("<img src={{ URL::to('/') }}/slider/" + html.data.image + " width='70' class='img-thumbnail' />");
                        $('#store_image').append("<input type='hidden' name='hidden_image' value='"+html.data.image+"' />");
                        $('#hidden_id').val(html.data.id);
                        $('.modal-title').text("Edit New Record");
                        $('#action_button').val("Edit");
                        $('#action').val("Edit");
                        $('#formModal').modal('show');
                    }
                })
            });

            var user_id;

            $(document).on('click', '.delete', function(){
                user_id = $(this).attr('id');
                $('#confirmModal').modal('show');
            });

            $('#ok_button').click(function(){
                $.ajax({
                    url:"allSlider/destroy/"+user_id,
                    beforeSend:function(){
                        $('#ok_button').text('Deleting...');
                    },
                    success:function(data)
                    {
                        setTimeout(function(){
                            $('#confirmModal').modal('hide');
                            $('#user_table').DataTable().ajax.reload();
                        }, 2000);
                    }
                })
            });

        });
    </script>
@endpush
