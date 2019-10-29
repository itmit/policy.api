@extends('layouts.adminApp')

@section('content')
    <h1>Создание опроса</h1>
    <div class="col-sm-12">
        <form class="form-horizontal" method="POST" action="{{ route('auth.polls.store') }}">
            {{ csrf_field() }}

            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name" class="col-md-4 control-label">Наименование</label>

                <div class="col-md-6">
                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required
                           autofocus>

                    @if ($errors->has('name'))
                        <span class="help-block">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                <label for="description" class="col-md-4 control-label">Описание</label>

                <div class="col-md-6">
                    <textarea name="description" id="description" cols="30" rows="10" style="resize: none" class="form-control" placeholder=" Необязательно"></textarea>

                    @if ($errors->has('description'))
                        <span class="help-block">
                            <strong>{{ $errors->first('description') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
                <label for="category" class="col-md-4 control-label">Категория опроса</label>

                <div class="col-md-6">
                    <select name="category" id="category" class="form-control" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id}}">{{ $category->name}}</option>
                        @endforeach
                    </select>

                    @if ($errors->has('category'))
                        <span class="help-block">
                            <strong>{{ $errors->first('category') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('link') ? ' has-error' : '' }}">
                <label for="link" class="col-md-4 control-label">Ссылка на опрос</label>

                <div class="col-md-6">
                    <input type="text" name="link" id="link" class="form-control" value="{{ old('link') }}" placeholder=" Необязательно">

                    @if ($errors->has('link'))
                        <span class="help-block">
                            <strong>{{ $errors->first('link') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('radio') ? ' has-error' : '' }}">
                <div class="col-md-6">
                    <input type="radio" id="unlimited" name="time" value="unlimited" checked>
                    <label for="unlimited">Бессрочно</label>

                    <input type="radio" id="limited" name="time" value="limited">
                    <label for="limited">с датой начала и завершения</label>
                </div>
            </div>

            <div class="form-group{{ $errors->has('start_at') ? ' has-error' : '' }}">
                <label for="start_at" class="col-md-4 control-label">Дата начала</label>

                <div class="col-md-6">
                    <input id="start_at" type="date" class="form-control" name="start_at" value="{{ old('start_at') }}"
                           autofocus disabled>

                    @if ($errors->has('start_at'))
                        <span class="help-block">
                            <strong>{{ $errors->first('start_at') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('end_at') ? ' has-error' : '' }}">
                <label for="end_at" class="col-md-4 control-label">Дата завершения</label>

                <div class="col-md-6">
                    <input id="end_at" type="date" class="form-control" name="end_at" value="{{ old('end_at') }}"
                           autofocus disabled>

                    @if ($errors->has('end_at'))
                        <span class="help-block">
                            <strong>{{ $errors->first('end_at') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <hr>
            <h2>Создание вопросов</h2>

            <div class="list_of_questions">
                
                    <div class="question">
                        <div class="question_name col-md-4">
                            <input type="text" name="question_name" placeholder=" Вопрос" class="form-control" required>
                        </div>
                        <div class="answers_container">
                            <div class="answers">
                                <div class="answer col-md-5 offset-md-1">
                                    <input type="text" name="answer" placeholder=" Ответ" class="form-control" required>
                                </div>
                                <div class="answer col-md-5 offset-md-1">
                                    <input type="text" name="answer" placeholder=" Ответ" class="form-control" required>
                                </div>
                            </div>
                            <div class="add-answer col-md-5 offset-md-1">
                                <input type="button" value="Добавить ответ" class="add_answer">
                            </div>
                        </div>
                    </div>

            </div>

            <input type="button" value="Добавить вопрос" class="add_new_question">

            <div class="form-group">
                <div class="col-md-6 col-md-offset-4">
                    <button type="submit" class="btn btn-primary">
                        Создать опрос
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
    $(document).ready(function() {
        $(document).on('change', $('input:radio[name=time]'), function() {
            if($('input:radio[name=time]:checked').val() == 'limited')
            {
                $("#start_at").prop("disabled", false);
                $("#end_at").prop("disabled", false);

                $("#start_at").prop("required", true);
                $("#end_at").prop("required", true);
            }
            if($('input:radio[name=time]:checked').val() == 'unlimited')
            {
                $("#start_at").prop("disabled", true);
                $("#end_at").prop("disabled", true);

                $("#start_at").prop("required", false);
                $("#end_at").prop("required", false);
            }

        });

        $(".add_new_question").on("click", function() {
            let elem = document.createElement('div');
            elem.append(sergay.content.cloneNode(true));

            // document.body.append(elem);
            $('.list_of_questions').append(elem);
            // $('.list_of_questions').append('<div class="question"><div class="question_name col-md-4"><input type="text" name="question_name" placeholder=" Вопрос" class="form-control"></div><div class="answer col-md-5 offset-md-1"><input type="text" name="answer" placeholder=" Ответ" class="form-control"></div><div class="answer col-md-5 offset-md-1"><input type="text" name="answer" placeholder=" Ответ" class="form-control"></div><div class="add-answer col-md-5 offset-md-1"><input type="button" value="Добавить ответ" class="add_answer"></div><div><input type="button" value="Удалить вопрос" class="col-md-4 delete_question"></div></div>');
        });

        $(".list_of_questions").on("click", ".delete_question", function(e) {
            $(this).closest(".question").remove();
        });

        $(".list_of_questions").on("click", ".add_answer", function(e) {
            let elem = document.createElement('div');
            elem.append(radik.content.cloneNode(true));
            $(this).closest(".answers_container").find('.answers').append(elem);
            // $(this).closest(".answers_container").find('.answers').append('new');
        });

    })
    </script>

    <style>
        .question {
            margin-bottom: 10px;
        }
        .material-icons {
            cursor: pointer;
        }
        input {
            margin-bottom: 10px;
        }
    </style>

    <template id="sergay">
        <hr>
        <div class="question">
            <div class="question_name col-md-4">
                <input type="text" name="question_name" placeholder=" Вопрос" class="form-control" required>
            </div>
            <div class="answers_container">
                <div class="answers">
                    <div class="answer col-md-5 offset-md-1">
                        <input type="text" name="answer" placeholder=" Ответ" class="form-control" required>
                    </div>
                    <div class="answer col-md-5 offset-md-1">
                        <input type="text" name="answer" placeholder=" Ответ" class="form-control" required>
                    </div>
                </div>
                <div class="add-answer col-md-5 offset-md-1">
                    <input type="button" value="Добавить ответ" class="add_answer">
                </div>
            </div>
            <div>
                <input type="button" value="Удалить вопрос" class="col-md-4 delete_question">
            </div>
        </div>
    </template>

    <template id="radik">
        <div class="answer col-md-5 offset-md-1">
            <input type="text" name="answer" placeholder=" Ответ" class="form-control" required>
        </div>
        <div class="answer col-md-1 offset-md-6">
            <span><i class="material-icons">delete</i></span>
        </div>
    </template>
    
@endsection