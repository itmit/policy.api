@extends('layouts.adminApp')

@section('content')
<div class="col-sm-9 tabs-content">
    <div class="row justify-content-center cont-m">
        <div class="col-md-12">
           <div class="group-btn-card">
                <a href="{{ route('auth.susliks.create') }}" class="btn-card">Создать суслика</a>
                <a href="{{ route('auth.createCategory') }}" class="btn-card">Создать категорию</a>
            </div>
            <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('auth.uploadSusliks') }}">
                {{ csrf_field() }}

                <br>

                <div class="row form-group{{ $errors->has('file') ? ' has-error' : '' }}">

                    <label for="file" class="col-md-4 form-control-file">.zip-папка для импорта</label>
        
                    <div class="col-md-6">
                        <input type="file" name="file" id="file" accept=".zip">
                    </div>
        
                    @if ($errors->has('file'))
                        <span class="help-block">
                            <strong>{{ $errors->first('file') }}</strong>
                        </span>
                    @endif
                </div>
        
                <div class="form-group">
                    <button type="submit" class="btn-card btn-tc-ct">
                            Загрузить сусликов из .zip
                    </button>
                </div>
            </form>

            <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('auth.uploadSusliksJSON') }}">
                {{ csrf_field() }}

                <br>

                <div class="row form-group{{ $errors->has('file') ? ' has-error' : '' }}">
                    <label for="file" class="col-md-4 form-control-file">.json файл для импорта</label>
        
                    <div class="col-md-6">
                        <input type="file" name="file" id="file" accept=".json">
                        <select name="category" class="form-control">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
        
                    @if ($errors->has('file'))
                        <span class="help-block">
                            <strong>{{ $errors->first('file') }}</strong>
                        </span>
                    @endif
                </div>
        
                <div class="form-group">
                    <button type="submit" class="btn-card btn-tc-ct">
                            Загрузить сусликов из .json
                    </button>
                </div>
            </form>

            <button type="button" class="btn-card btn-tc-danger js-destroy-button">Удалить отмеченных сусликов</button>
            {{-- <button type="button" class="btn-card btn-tc-danger js-clear-dir-button">Очистить каталог</button> --}}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <select name="category" class="form-control suslik-by-category">
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        <table class="table policy-table">
                    <thead>
                    <tr>
                        <th><input type="checkbox" name="destroy-all-susliks" class="js-destroy-all"/></th>
                        <th>Имя</th>
                        <th>Категория</th>
                        <th>Место работы</th>
                        <th>Должность</th>
                        <th>Ссылка</th>
                        <th>Лайки</th>
                        <th>Дизлайки</th>
                        <th>Нейтралы</th>
                        <th>Дата создания</th>
                        <th><span class="material-icons">create</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    {{-- @foreach($susliks as $suslik)
                    @if($suslik->category() == NULL)
                    @continue
                    @endif
                        <tr>
                            <td><input type="checkbox" data-suslik-id="{{ $suslik->id }}" name="destoy-suslik-{{ $suslik->id }}" class="js-destroy"/></td>
                            <td>{{ $suslik->name }}</td>
                            <td>{{ $suslik->category()->name }}</td>
                            <td>{{ $suslik->place_of_work }}</td>
                            <td>{{ $suslik->position }}</td>
                            <td><a href="{{ $suslik->link }}" target="_blank">ссылка</a></td>
                            <td>{{ $suslik->likes }}</td>
                            <td>{{ $suslik->dislikes }}</td>
                            <td>{{ $suslik->neutrals }}</td>
                            <td>{{ $suslik->created_at->timezone('Europe/Moscow') }}</td>
                            <td><span class="material-icons"><a href="susliks/{{ $suslik->id }}/edit">create</a></span></td>
                        </tr>
                    @endforeach --}}
                    </tbody>
                </table>
    </div>
</div>
<script>
$(document).ready(function() {
    $(function(){
        $(".js-destroy-all").on("click", function() {

            if($(".js-destroy-all").prop("checked")){
                $(".js-destroy").prop("checked", "checked");
            }
            else{
                $(".js-destroy").prop("checked", "");
            }
        });
    });

    $(document).on('click', '.js-destroy-button', function() {
        let ids = [];

        $(".js-destroy:checked").each(function(){
            ids.push($(this).data('suslikId'));
        });

        $.ajax({
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            dataType: "json",
            data    : { ids: ids },
            url     : 'susliks/delete',
            method    : 'delete',
            success: function (response) {
                console.log(response);
                $(".js-destroy:checked").closest('tr').remove();
                $(".js-destroy").prop("checked", "");
            },
            error: function (xhr, err) { 
                console.log("Error: " + xhr + " " + err);
            }
        });

    });

    $(document).on('click', '.js-clear-dir-button', function() {
        $.ajax({
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            dataType: "json",
            url     : 'susliks/clearDir',
            method    : 'get',
            success: function (response) {
                alert('Директория очищена!')
            },
            error: function (xhr, err) { 
                console.log("Error: " + xhr + " " + err);
            }
        });

    });

    $(document).on('change', '.suslik-by-category', function() {
        console.log('category');
        let category = $(this).children("option:selected").val();
        console.log(category);
        // $.ajax({
        //     headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        //     dataType: "json",
        //     url     : 'susliks/getSusliksByCategory',
        //     data    : {category: category}
        //     method    : 'post',
        //     success: function (response) {
        //         alert('Директория очищена!')
        //     },
        //     error: function (xhr, err) { 
        //         console.log("Error: " + xhr + " " + err);
        //     }
        // });
    });
})
</script>



@endsection
