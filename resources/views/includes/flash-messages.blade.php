@if (session('success'))
    <div class="note note-green note-with-right-icon text-white">
        <div class="note-icon"><i class="fa fa-check-circle"></i></div>
        <div class="note-content text-right">
            <h4><b>{{session('success')}}</b></h4>
        </div>
    </div>
@endif
@if (session('error'))
    <div class="note note-danger note-with-right-icon text-white">
        <div class="note-icon"><i class="fa fa-times-circle"></i></div>
        <div class="note-content text-right">
            <h4><b>{{session('error')}}</b></h4>
        </div>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if (session('warning'))
    <div class="note note-warning note-with-right-icon text-white">
        <div class="note-icon"><i class="fa fa-warning"></i></div>
        <div class="note-content text-right">
            <h4><b>{{session('warning')}}</b></h4>
        </div>
    </div>
@endif
@if (session('info'))
    <div class="note note-info note-with-right-icon text-white">
        <div class="note-icon"><i class="fa fa-check-circle"></i></div>
        <div class="note-content text-right">
            <h4><b>{{session('info')}}</b></h4>
        </div>
    </div>
@endif
