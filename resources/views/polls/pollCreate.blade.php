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
                        <div class="row">
                            <div class="question_name col-md-4">
                                <input type="text" name="question_name" placeholder=" Вопрос" class="form-control" required>
                            </div>
                            <div class="question_option_multiple col-md-3">
                                <input type="checkbox" name="multiple"> Множественный
                            </div>
                            <div class="question_option_other col-md-5">
                                <input type="checkbox" name="other"> Включает вариант ответа "другой"
                            </div>
                        </div>
                        
                        <div class="answers_container">
                            <div class="answers">
                                <div class="row">
                                    <div class="answer col-md-5 offset-md-1">
                                        <input type="text" name="answer" placeholder=" Ответ" class="form-control" required>
                                    </div>
                                </div>
                
                                <div class="row">
                                    <div class="answer col-md-5 offset-md-1">
                                        <input type="text" name="answer" placeholder=" Ответ" class="form-control" required>
                                    </div>
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
                    <input type="button" value="Создать опрос" class="test">
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
            $('.list_of_questions').append(sergay.content.cloneNode(true));
            // $('.list_of_questions').append('<div class="question"><div class="question_name col-md-4"><input type="text" name="question_name" placeholder=" Вопрос" class="form-control"></div><div class="answer col-md-5 offset-md-1"><input type="text" name="answer" placeholder=" Ответ" class="form-control"></div><div class="answer col-md-5 offset-md-1"><input type="text" name="answer" placeholder=" Ответ" class="form-control"></div><div class="add-answer col-md-5 offset-md-1"><input type="button" value="Добавить ответ" class="add_answer"></div><div><input type="button" value="Удалить вопрос" class="col-md-4 delete_question"></div></div>');
        });

        $(".list_of_questions").on("click", ".delete_question", function(e) {
            $(this).closest(".question").remove();
        });

        $(".list_of_questions").on("click", ".add_answer", function(e) {
            // let elem = document.createElement('div');
            // elem.append(radik.content.cloneNode(true));
            $(this).closest(".answers_container").find('.answers').append(radik.content.cloneNode(true));
            // $(this).closest(".answers_container").find('.answers').append('new');
        });

        $(".list_of_questions").on("click", ".delete-answer", function(e) {
            $(this).closest(".row").remove();
        });

        $(document).on("click", ".test", function(e) {
        // $("form").submit(function(e) {

            let all_data = new Map([
            ['name', $("input[name='name']").val()],
            ['description', $("[name='description']").val()],
            ['category', $("[name='category']").val()],
            ['link', $("input[name='link']").val()]
            ]);  

            if($('input:radio[name=time]:checked').val() == 'limited')
            {
                all_data.set('start_at', $("input[name='start_at']").val());
                all_data.set('end_at', $("input[name='end_at']").val());
            }
            if($('input:radio[name=time]:checked').val() == 'unlimited')
            {
                all_data.set('start_at', null);
                all_data.set('end_at', null);
            }

            let question_number = 0;

            let all_questions = new Map();
            $( ".question" ).each(function( index ) {
                let question_data = new Map(); 
                let answer_data = new Map();  

                question_data.set('question_name', $(this).find("input[name='question_name']").val());
                question_data.set('multiple', $(this).find("input[name='multiple']").prop('checked'));
                question_data.set('other', $(this).find("input[name='other']").prop('checked'));

                let i = 0;
                $(this).find('.answer').each(function( index ) {
                    answer_data.set(i, $(this).find("input[name='answer']").val());
                    i++;
                });

                question_data.set('answers', answer_data);
                question_data = Object.fromEntries(question_data)
                // data.set('question'+question_number, question_data);
                all_questions.set('question'+question_number, question_data);
                
                question_number++;
            });

            all_questions = Object.fromEntries(all_questions)
            all_data.set('questions', all_questions);
            // all_data_array = JSON.stringify(all_data);
            const all_data_array = Object.fromEntries(all_data);

            $.ajax({
                headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType: "html",
                data    : {all_data : all_data_array},
                url     : '../polls',
                method    : 'post',
                success: function (response) {
                    // console.log(data);
                    console.log(response);
                },
                error: function (xhr, err) { 
                    console.log("Error: " + xhr + " " + err);
                }
            });
        });

    })
    </script>

    <style>
        .question {
            margin-bottom: 10px;
        }
        .material-icons {
            cursor: pointer;
            padding-top: 7px;
        }
        input {
            margin-bottom: 10px;
        }
    </style>

    <template id="sergay">
        <div class="question">
            <hr>
            <div class="row">
                <div class="question_name col-md-4">
                    <input type="text" name="question_name" placeholder=" Вопрос" class="form-control" required>
                </div>
                <div class="question_option_multiple col-md-3">
                    <input type="checkbox" name="multiple"> Множественный
                </div>
                <div class="question_option_other col-md-5">
                    <input type="checkbox" name="other"> Включает вариант ответа "другой"
                </div>
            </div>
            <div class="answers_container">
                <div class="answers">
                    <div class="row">
                        <div class="answer col-md-5 offset-md-1">
                            <input type="text" name="answer" placeholder=" Ответ" class="form-control" required>
                        </div>
                    </div>
    
                    <div class="row">
                        <div class="answer col-md-5 offset-md-1">
                            <input type="text" name="answer" placeholder=" Ответ" class="form-control" required>
                        </div>
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
        <div class="row">
            <div class="answer col-md-5 offset-md-1">
                <input type="text" name="answer" placeholder=" Ответ" class="form-control" required>
            </div>
            <div class="answer-delete col-md-1">
                <i class="material-icons delete-answer">delete</i>
            </div>
        </div>
    </template>
    
@endsection