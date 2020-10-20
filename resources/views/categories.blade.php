<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ __('Dynamic Categories') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css" >
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="{{ asset('js/app.js') }}"></script>

        
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="header">
                        <h1>{{ __('Dynamic Categories') }}</h1>
                    </div>
                </div>
                
                <div class="col-12 last">
                    <div class="buttons">
                        <form method="post">
                            @csrf
                            <button type="button" class="btn btn-lg btn-outline-primary" data-toggle="modal" data-target="#addModal">{{ __('Add') }}</button>
                            <button type="button" class="btn btn-lg btn-outline-danger" data-toggle="modal" data-target="#removeModal">{{ __('Remove') }}</button>
                            <input id="hdnSelectedSubCategoryID" type="hidden" value="0" />
                            <input id="hdnSelectedLevel" type="hidden" value="0" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

<!-- Add Modal -->
<div id="addModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"></button>
      <h4 class="modal-title">{{ __('Add Subcategory') }}</h4>
      </div>
      <div class="modal-body">
        <p>{{ __('Add subcategory in ') }}<b><span class="SelectedSubCategoryName">{{ __('Root') }}</span></b></p>
        <input class="form-control" placeholder="Name of new subcategory" id="txtNewCategory" value="" type="text" />
      </div>
      <div class="modal-footer">
        <button id="add" type="button" class="btn btn-success">{{ __('Add') }}</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
      </div>
    </div>
  </div>
</div>

<!-- Remove Modal -->
<div id="removeModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"></button>
        <h4 class="modal-title">{{ __('Remove Category') }}</h4>
        </div>
        <div class="modal-body">
          <p>{{ __('Remove category ') }}<b><span class="SelectedSubCategoryName">{{ __('Root') }}</span></b></p>
          <p>It will remove all subcategories of this category</p>
        </div>
        <div class="modal-footer">
          <button id="remove" type="button" class="btn btn-success">{{ __('Remove') }}</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
        </div>
      </div>
    </div>
  </div>


<script type="text/javascript">
    $(document).ready(function() {

       getSubCategory('0','0'); 

       //When select the subcategory
        $(document).on('change','.subcategory',function(){
            oldlevel = $("#hdnSelectedLevel").val();
            $("#hdnSelectedSubCategoryID").val($(this).val() == "" ? $(this).attr("data-id") : $(this).val());
            $("#hdnSelectedLevel").val($(this).val() == "" ? parseInt($(this).attr("data-level")) : parseInt($(this).attr("data-level")) + 1);
            $(".SelectedSubCategoryName").html($(this).val() == "" ? $(this).attr("data-name") : $(this).find('option:selected').text());

            categoryid =  $("#hdnSelectedSubCategoryID").val();
            level = $("#hdnSelectedLevel").val();

            //remove levels div after selected subcategory
            for(i=level;i <= oldlevel;i++)
            {
                $('.level-' + i).remove();
            }

            getSubCategory(categoryid ,level);
        });

        //Add new subcategory
        $('#add').on('click', function() {
            categoryid =  $("#hdnSelectedSubCategoryID").val();
            level = $("#hdnSelectedLevel").val();
            name = $("#txtNewCategory").val();
            
            addSubCategory(categoryid,level,name);
            $("#txtNewCategory").val('');
        });

        //Add new subcategory
        $('#remove').on('click', function() {
            categoryid =  $("#hdnSelectedSubCategoryID").val();
            level = $("#hdnSelectedLevel").val();
            removeSubCategory(categoryid,level);
        });
            
        //Ajax to get subcategory
        function getSubCategory(categoryid ,level) {
            if(categoryid && level) {
                $.ajax({
                        url: '{{config('app.url')}}/ajax/getsubcategories/' + categoryid + '/' + level,
                        type: "GET",
                        success:function(data) {
                            $('.last').before(data);
                        }
                    });
            } 
        }

        //Ajax to add subcategory
        function addSubCategory(categoryid, level , name) {
            if(categoryid && level && name) {
                $.ajax({
                        url: '{{config('app.url')}}/ajax/addsubcategory',
                        type: "POST",
                        data: {_token: $('input[name=_token]').val() ,name: name, id: categoryid, level: level},
                        success:function(data) {
                            $('.level-' + level).remove();
                            getSubCategory(categoryid,level);
                        }
                    });
                    $('#addModal').modal('toggle');
            } 
            else {
                alert("{{ __('Please insert the name of subcategory') }}")
            }
        }

        //Ajax to remove subcategories
        function removeSubCategory(categoryid, level) {
            if(categoryid && level) {
                $.ajax({
                        url: '{{config('app.url')}}/ajax/removesubcategory',
                        type: "POST",
                        //dataType: "json",
                        data: {_token: $('input[name=_token]').val(), id: categoryid},
                        success:function(id) {
                            if(id == 'false'){
                                alert("{{ __('You can`t remove it') }}");
                            }
                            else {
                                $("#hdnSelectedSubCategoryID").val(id);
                                $("#hdnSelectedLevel").val(parseInt(level)-1);
                                $(".SelectedSubCategoryName").html($('.level-' + (parseInt(level)-1).toString() + ' select').attr("data-name")); 
                                $('.level-' + level).remove();
                                $('.level-' + (parseInt(level)-1)).remove();
                                getSubCategory(id ,(parseInt(level)-1).toString());
                            }
                        }
                    });

                    $('#removeModal').modal('toggle');
            }
        }
    });
</script>
