<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin.php");
CJSCore::Init(array("jquery3"));
CUtil::InitJSCore(array('window'));
IncludeModuleLangFile(__FILE__);

if(!$USER->IsAdmin())
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

// получаем настройки

// TODO переписать в блоке сопоставления разделов экшены как в свойствах

?>
<style>
    .content-wrapper {
        background-color: white;
        padding: 20px 30px;
    }

    label {
        display: block;
    }
    .summ-info {
        border: #f2dede solid 2px;
        background-color: white;
        padding: 5px 20px 29px;
        margin-bottom: 30px;
    }
    .step .content-block {
        display: none;
    }
    .step.active .content-block {
        display: block;
    }

    #sections ul {
        list-style: none;
        padding-left: 20px;
        padding-top: 4px;
    }
    #sections li {
        padding-top: 6px;
        margin-bottom: 6px;
    }

    .death-level-1 ul {display: none;}

    .show-sublevel{
        cursor: pointer;
    }
    .show-sublevel:before {
        content: ">";
        padding-right: 4px;
    }

    .buttons {display: flex;}
    .button {
        display: block;
        padding: 5px 11px;
        border: #3ac769 solid 1px;
        border-radius: 5px;
        font-size: 18px;
        margin: 5px;
        cursor: pointer;
        color: white;
        background-color: green;
        text-decoration: none;
    }
    .button a,
    .button a:hover,
    {
        color: inherit;#get-props .prop-name
        text-decoration: none;
    }
    .button:hover {text-decoration: none; background-color: #00be00; }
    .button.button-red{background-color: red;}
    .button.button-red:hover{background-color: #ff4508;}


    #sections .bind {
        display: inline-flex;
        align-items: center;
    }

    .show_select {
        display: flex;
        align-items: center;
    }


    #get-props li {
        display: flex;
    }

    #get-props .prop-name {width: 193px;}

    #get-props .actions {
        display: flex;
        margin-left: 20px;
    }

    #get-props .actions .action {
        padding: 7px 15px;

    }

    .action .reset,
    .action .go,
    .action .bind-into-create
    {
        cursor: pointer;
        color:blue;
        text-decoration: underline;
    }

    .action .reset,
    .action.process .go,
    .action.set .go
    {display: none;}
    .action.set .reset {display: block;}


    #get-file label {
        width: 153px;
        display: inline-block;
    }

    #get-file .field-group {
        margin-bottom: 20px;
    }

    #file_status {
        margin-left: 20px;
        padding: 5px 10px;
        background: red;
        color: white;
        font-weight: bold;
        border-radius: 5px;
        font-size: 15px;
    }
    #file_status.status_1 {
        background: green;
    }


</style>

<div class="content-wrapper">
    <section id="summ-info" class="summ-info">
        <h2>Общая информация</h2>

    </section>
    <section class="steps ">

        <section class="step global-options" id="global-options">
            <h3 class="step__title">ШАГ 0: Общие настройки</h3>
            <div class="content-block">
                В будущем здесь блок настроек. После первого заполнения скрыт по умолчанию
            </div>
        </section>

        <section class="step get-file" id="get-file">
            <h3 class="step__title">ШАГ 1: выбор файла</h3>
            <div class="content-block" style="display: block">
                <fieldset>
                    <legend>Укажите источник</legend>
                    <div class="field-group">
                        <label for="shop-file">Выберите из магазинов:</label>
                        <select id="shop-file" name="file" >
                            <option selected disabled>  Выберите...</option>
                        </select>
                    </div>

                    <div class="field-group">
                        <label for="manual-file">Или укажите вручную:</label>
                        <input id="manual-file" type="text"  name="file" size="100" />
                    </div>
                </fieldset>

                <div class="buttons">
                    <a href="#" class="button prev">назад</a>
                    <a href="#" class="button next">далее</a>
                </div>
            </div>
        </section>

        <section class="step get-sections" id="get-sections" >
            <h3 class="step__title">ШАГ 2: Выбор разделов и ИБ куда грузить</h3>

            <?php // TODO Добавить создание раждела на второй шаг ?>

            <div class="content-block">
                <div class="warning">
                    ВАЖНО!!! Товары для несопоставленных разделов загружаться не будут
                    НО если раздел не настроен, но раздел выше настрон, будут взяты его натройки
                </div>
                <div id="sections" class="sections">
                    <ul class="death-level-1">
                    </ul>
                </div>
                <div class="buttons">
                    <a href="#" class="button prev">назад</a>
                    <a href="#" class="button save">Сохранить</a>
                    <a href="#" class="button next">далее</a>
                </div>
            </div>

        </section>

        <section class="step get-props" id="get-props">
            <h3 class="step__title">ШАГ 3: Сопставление св-в</h3>

            <div class="content-block">
                <div class="warning">
                    Вам нужно повторить сопоставление св-в и полей для каждого выбранноо ИБ и при необходимости для SKU<br>
                    Поля - это стандартные св-ва элементов битрикса, они перечислены на вкладке "Поля" в настройках ИБ<br>
                    Св-ва - это созданные вручную дополнительные св-ва, они перечислены на вкладке "Свойства" в настройках ИБ
                </div>

                <div class="iblocks">

                    <div>
                        <p>Однозначно понятные поля и св-ва уже связаны с соответствующими системными св-вами</p>
                        <ul>
                            <li>Цена - Цена</li>
                            <li>вес - вес</li>
                            <li>валюта - валюта</li>
                        </ul>

                    </div>



                </div>

                <div class="buttons">
                    <a href="#" class="button prev">назад</a>
                    <a href="#" class="button save">Сохранить</a>
                    <a href="#" class="button next">далее</a>
                </div>

            </div>


        </section>

        <section class="step get-options" id="get-options">
            <h3 class="step__title">ШАГ 4: Настройки обновления существующих</h3>
            Для запуска скрипта после обрыва предыдущей загрузки снимите все опции
            <div class="content-block">
                <form action="">
                    <fieldset class="product-update-options">
                        <legend>Выбирите что обновлять у обнаруженныж товаров</legend>
                        <label for="prod_props_upd">
                            <input type="checkbox" name="prod_props_upd" id="prod_props_upd">обновлять св-ва товаров (без картинок)</label>
                        <label for="prod_image_upd">
                            <input type="checkbox" name="prod_image_upd" id="prod_image_upd">Обновлять картинки</label>
                        <label for="force_moving_upd">
                            <input type="checkbox" name="force_moving_upd" id="force_moving_upd">Принудительно переместить товары в разделы как указано на втором шаге, даже если они вручную перемещены в др раздел или инфоблок</label>
                    </fieldset>
                    <fieldset class="sku-update-options">
                        <legend>Выбирите что обновлять у обнаруженныж SKU</legend>
                        <label for="sku_price_upd">
                            <input type="checkbox" name="sku_price_upd" id="sku_price_upd" checked >обновлять цену</label>
                        <label for="sku_props_upd">
                            <input type="checkbox" name="sku_props_upd" id="sku_props_upd">обновлять св-ва SKU</label>
                    </fieldset>
                </form>


                <div class="buttons">
                    <a href="#" class="button prev">назад</a>
                    <a href="#" class="button start">Запуск</a>
                </div>

            </div>
        </section>

    </section>
</div>

<script>

    // TODO где-то нужно добавить проверку на натичие и зоздание обязательных св-в. Например URL, Модель или Артикул

    let module_path = "/local/modules/zvezda.importproductsxml/tools/";
    let shop_name = '';
    let file_path_remote = '';
    let file_path_local = '';
    let file_remote_status;
    let relations_iblocks;
    let options_data_for_scritp = {};

    setOptions('offersInIteration', 10);
    setOptions('firstOfferIter', 0);
    setOptions('object_id', 0); //TODO id магазина, если выбран
    setOptions('file_id', 0); //TODO id файла из hlbl если это не магазин

    //console.log(options_data_for_scritp);

    // getOptions();

    showShops();

    $('#get-file').on('click', '.button.next', function () {
        let current_step = $(this).closest('.step');
        let next_step = current_step.next('.step');
        let shop_file = $('#shop-file').val();
        let manual_file = $('#manual-file').val();
        let file_url;
        shop_name = $('#shop-file').find('option:selected').text();

        if( manual_file != '' ){
            file_path_remote = manual_file;
            type_file = 'manual';
        }
        else if( shop_file !== null) {
            file_path_remote = shop_file;
        }
        else{
            alert( 'Не выбран файл' );
            return false;
        }

        if( shop_name !== '' ){
            addInfo( '<div id="shop-name"><b>Выбран магазин:</b> '+shop_name+'</div>' );
        }
        addInfo( '<div id="file_url"><b>Файл:</b> '+file_path_remote+'<span id="file_status"></span></div>' );
        afterStepGetFile(current_step);

    });

    $('#get-sections').on('click', '.button.next', function () {
        let current_step = $(this).closest('.step');
        let next_step = current_step.next('.step');

        afterStepSections();
        beforeStepProps();
        current_step.find('.content-block').hide();
        next_step.find('.content-block').show();
    });

     /*$('#get-sections').on('click', '.button.save', function () {
         console.log(options_data_for_scritp)
     });*/

    $('#get-props').on('click', '.button.next', function () {
        let current_step = $(this).closest('.step');
        let next_step = current_step.next('.step');

        afterStepProps();
        beforeStepOptions();

        current_step.find('.content-block').hide();
        next_step.find('.content-block').show();
    });

    // $('#get-props').on('click', '.button.save', function () {
    //
    //     let options_container = $('#get-props');
    //
    //     let options = {};
    //
    //     $.each( options_container.find('li'), function(index,value){
    //         let item = $(value);
    //         let prop_name = item.find('.prop-name').text();
    //         let prod_field = item.find('[data-action=field-product]').find('.choice');
    //         let prod_prop = item.find('[data-action=prop-product]').find('.choice');
    //         let sku_prop = item.find('[data-action=prop-sku]').find('.choice');
    //
    //         let choices = item.find('.choice');
    //
    //         if( choices.length > 0 ){
    //             options[prop_name] = {};
    //             if( prod_field.length > 0 ) {
    //                 let prod_field_id = prod_field.attr('data-id');
    //                 options[prop_name]['prod_field_id'] = prod_field_id;
    //             }
    //             if( prod_prop.length > 0 ) {
    //                 let prod_prop_id = prod_prop.attr('data-id');
    //                 options[prop_name]['prod_prop_id'] = prod_prop_id;
    //             }
    //             if( sku_prop.length > 0 ) {
    //                 let sku_prop_id = sku_prop.attr('data-id');
    //                 options[prop_name]['sku_prop_id'] = sku_prop_id;
    //             }
    //         }
    //
    //     });
    //     console.log( options );
    //
    //
    // });

    $('#get-options').on('click', '.button.start', function () {
        let current_step = $(this).closest('.step');
        let next_step = current_step.next('.step');

        afterStepOptions();
        beforeScriptStart();
    });


    $('#sections').on( 'click', '.js_addlevel', function(){
        let container = $(this).closest('.bind');
        let container_select = container.find('.show_select');
        let container_path = container.find('.path');
        let path_cnt =  container_path.find('span').length;
        let iblock_id = "";
        let section_id = "";
        if( path_cnt > 0  )
            iblock_id = container_path.find('span').filter(':first').attr('data-id');
        if( path_cnt > 1 )
            section_id = container_path.find('span').filter(':last').attr('data-id');

        $(this).remove();
        container_select.append('<select></select><div class="button js_apply">v</div><div class="button button-red js_breack">x</div>');
        showSectionsFromIblock(container_select.find('select'), iblock_id, section_id);
        return false;
    });

    $('#sections').on( 'click', '.js_apply', function(){
        let container = $(this).closest('.bind');
        let id = container.find('select').val();
        let name = container.find('select option:selected').text();

        container.find('.show_select').empty();

        container.find('.path').append('/<span data-id="'+id+'">'+name+'</span>');
        container.closest('li').addClass('completed');
        container.append('<a class="js_addlevel" href="#">Добавить уровень</a>');
    });

    $('#sections').on( 'click', '.js_breack', function(){
        let container = $(this).closest('.bind');
        container.find('.show_select').empty();
        container.append('<a class="js_addlevel" href="#">+</a>');
    });

    $('#sections').on( 'click', '.js_remove', function(){
        let container = $(this).closest('td');
        remove(container);
    });

    $('#sections').on( 'click', '.js_choose_sect', function(){
        let container = $(this).closest('td');
        let select_container;

        $(this).detach();
        container.append('<select class="show_select"></select>');
        select_container = container.find('.show_select');
        showSectionsFromIblock(select_container);
        container.append('<div class="js_applay">applay</div>');
    });

    $('#sections').on('click', '.show-sublevel', function () {
        $(this).siblings('ul').slideToggle();
    });


    $(document).on('click', '.bind .go', function(){
        let context = $(this).closest('.action');
        let action = context.attr('data-action');
        let iblock_id = $(this).closest('.iblock-container').attr('data-iblock-id');
        $.ajax({
            method: "POST",
            url: module_path + "ajaxGetPropsFromIblocks.php",
            dataType: 'json',
            context: context,
            data:{ iblock_id:iblock_id, need: action },
            success: function(data){
                processAction(context, data['HTML'] );
            },
            error: function(response){
                // $("#result #error").show();
                // setTimeout('$("#result #error").hide()', 5000);
            }
        });

    });

    $(document).on('click', '.create .go', function(){
        let context = $(this).closest('.action');
        let action = context.attr('data-action');
        let iblock_id = $(this).closest('.iblock-container').attr('data-iblock-id');

        let html = '<form action=""><input type="text" name="NAME" placeholder="Название св-ва"><input type="text" name="CODE" placeholder="Симв. код св-ва"></form>';
        processAction ( context, html  );

    });

    $(document).on('click', '.reset', function(){
        resetAction($(this));
    });

    $(document).on('click', '.bind .applay', function(){
        let action_block = $(this).closest('.action');
        let id = action_block.find('select').val();
        let name = action_block.find('select option:selected').text();
        finishAction( action_block, id, name );
    });

    $(document).on('click', '.create .applay', function(){
        let action_block = $(this).closest('.action');
        let form = action_block.find('form');
        let name = form.find('[name=NAME]').val();
        let code = form.find('[name=CODE]').val();
        let iblock_id = action_block.closest('.iblock-container').attr('data-iblock-id');

        propCreate( action_block, iblock_id, name, code );

    });

    $(document).on('click', '.bind-into-create', function() {

        let action_block = $(this).closest('.action');
        let id = $(this).attr('data-id');
        let name =  $(this).attr('data-name');
        finishAction(action_block, id, name);
    })

    $(document).on('click', '.process-block .break', function(){
        let action_block = $(this).closest('.action');
        action_block.find('.process-block').remove();
        action_block.toggleClass('process');
    });


    function afterStepGetFile(current_step){
        showRemoteFileStatus();
        getFile();
        setFileOptions(current_step);
    }

    function beforeStepSections( current_step ){
        let next_step = current_step.next('.step');

        showSectionsFromFile(); // Сюда вставляем блок шага

        current_step.find('.content-block').hide();
        next_step.find('.content-block').show();
    }

    function afterStepSections(){
        // TODO проверка условий заполнения
        // TODO Если не сохранено - спросить, нужно ли сохранить или это одноразовые настройки
        // TODO запись данных в БД, если нужно сохранить

        let sections = {};
        // let section_items = $('#sections').find('li');
        $.each( $('#sections').find('li'), function(index,value){
            let file_sect_id = $(this).attr('data-filesectionid');
            let path = $(this).children('.bind').find('.path');

            if( path.find('span').length === 0 ){
                if( path.closest('.completed','#sections').length !== 0){
                    path = path.closest('.completed','#sections').children('.bind').find('.path');
                }
            }
            if( path.find('span').length > 0){
                let iblock_id = path.find('span').filter(':first').attr('data-id');
                sections[file_sect_id] = {};
                sections[file_sect_id]['iblock_id'] = iblock_id;
            }
            if( path.find('span').length > 1){
                let section_id = path.find('span').filter(':last').attr('data-id');
                sections[file_sect_id]['section_id'] = section_id;
            }
        });
        setOptions('sections', sections);
        // console.log(getOptions());sections

        let paths = $('#sections').find('.path');
        relations_iblocks = [];
        paths.each(function( index, value) {
            let iblock_id = $(value).find('span:first').attr('data-id')
            if( iblock_id !== undefined )
                relations_iblocks.push(iblock_id);
        });
        $('#relations_iblocks').remove();
        addInfo( '<div id="relations_iblocks" class="relations_iblocks"><h4>Для загрузки выбраны ИБ-ки</h4><ul class="content"></ul></div>'  );
        $.ajax({
            method: "POST",
            url: module_path + "beforeStepProps.php",
            dataType: 'json',
            data:{iblock_ids:relations_iblocks},
            success: function(data){
                setOptions('relations_iblocks', data['ITEMS']);
                // console.log(getOptions());
                $.each(data['ITEMS'], function(index,value){
                    addInfo( '<li>'+value['NAME']+' ('+value['NAME']+')</lI>', $('#relations_iblocks').find('.content') );
                });
            },
            error: function(response){
                // $("#result #error").show();
                // setTimeout('$("#result #error").hide()', 5000);
            }
        })
    }

    function beforeStepProps(){

        if( file_path_local != '' ){
            $.ajax({
                method: "POST",
                url: module_path + "ajaxGetPropsFromFile.php",
                dataType: 'json',
                // context: context,
                data:{file_path:file_path_local},
                success: function(data){
                    // TODO отработать статус data['STATUS']
                    // TODO Показать сообщение data['MASSAGE']
                    // TODO визуализировать многоуровневость
                    let container = $('#get-props');
                    let container_iblocks = $('#get-props').find('.iblocks');
                    let fields = data['ITEMS']['FIELDS'];
                    let props  = data['ITEMS']['PROPS'];
                    let actions = '';

                    $.each( relations_iblocks, function(index,value){

                        container_iblocks.append('<div class="iblock-container" data-iblock-id="' + value + '"><h3>Сопоставление для ИБ "' + value + '"</h3><h4>поля товаров из файла</h4><ul class="fields"></ul><h4>Св-ва товаров из файла</h4><ul class="props"></ul></div>');
                        let container_fields = container_iblocks.find('[data-iblock-id=' + value + ']').find('.fields');
                        let container_props  = container_iblocks.find('[data-iblock-id=' + value + ']').find('.props');
                        // if( !fields.isEmptyObject() ){
                            $.each( fields, function(index,value){
                                container_fields.append('<li><div class="prop-name">' + value + '</div><div class="bind-block"><div class="actions"></div></div></li>');
                            });
                        // }

                        $.each( props, function(index,value){
                            container_props.append('<li><div class="prop-name">' + value + '</div><div class="bind-block"><div class="actions"></div></div></li>');
                        });
                    });

                    let container_actions = container.find('.actions');
                    setAction( 'field-product', 'bind', 'Связать c полем товара', container_actions );
                    setAction( 'prop-product', 'bind', 'Связать со св-вом товара', container_actions );
                    setAction( 'prop-product', 'create', 'создать новое св-во товара', container_actions );
                    setAction( 'prop-sku', 'bind', 'Связать со св-ом SKU', container_actions );
                    setAction( 'prop-sku', 'create', 'создать новое св-во SKU', container_actions );
                },
                error: function(response){
                    // $("#result #error").show();
                    // setTimeout('$("#result #error").hide()', 5000);
                }
            })
        }

    }

    function afterStepProps(){
        let field_options = $('#get-props').find('ul.fields');
        let props_options = $('#get-props').find('ul.props');
        let options = {};
        options['fields'] = {};
        options['params'] = {};

        $.each( field_options.find('li'), function(index,value){
            let item = $(value);
            let prop_name = item.find('.prop-name').text();
            let prod_field = item.find('[data-action=field-product]').find('.choice');
            let prod_prop = item.find('[data-action=prop-product]').find('.choice');
            let sku_prop = item.find('[data-action=prop-sku]').find('.choice');

            if( item.find('.choice').length > 0 ){
                options['fields'][prop_name] = {};
                if( prod_field.length > 0 ) {
                    options['fields'][prop_name]['prod_field_id'] = prod_field.attr('data-id');
                }
                if( prod_prop.length > 0 ) {
                    options['fields'][prop_name]['prod_prop_id'] = prod_prop.attr('data-id');
                }
                if( sku_prop.length > 0 ) {
                    options['fields'][prop_name]['sku_prop_id'] = sku_prop.attr('data-id');
                }
            }
        });

        $.each( props_options.find('li'), function(index,value){
            let item = $(value);
            let prop_name = item.find('.prop-name').text();
            let prod_field = item.find('[data-action=field-product]').find('.choice');
            let prod_prop = item.find('[data-action=prop-product]').find('.choice');
            let sku_prop = item.find('[data-action=prop-sku]').find('.choice');

            if( item.find('.choice').length > 0 ){
                options['params'][prop_name] = {};
                if( prod_field.length > 0 ) {
                    options['params'][prop_name]['prod_field_id'] = prod_field.attr('data-id');
                }
                if( prod_prop.length > 0 ) {
                    options['params'][prop_name]['prod_prop_id'] = prod_prop.attr('data-id');
                }
                if( sku_prop.length > 0 ) {
                    options['params'][prop_name]['sku_prop_id'] = sku_prop.attr('data-id');
                }
            }
        });

        setOptions('options', options );
    }

    function beforeStepOptions(){
        // Здась пока нет идей
        return true;
    }

    function afterStepOptions(){
        let container = $('#get-options');
        let product_options_container = container.find('.product-update-options');
        let sku_options_container = container.find('.sku-update-options');
        let update_options = {};
        update_options['product'] = [];
        update_options['sku'] = [];

        product_options_container.find('input:checkbox:checked').each(function(){
            update_options['product'].push($(this).attr('name'));
        });
        sku_options_container.find('input:checkbox:checked').each(function(){
            update_options['sku'].push($(this).attr('name'));
        });

        // console.log(update_options);
        setOptions('update_options', update_options);
    }

    function beforeScriptStart(){
        // TODO Здесь собираем все настройки, возможно инициируем сессию, если не в таблице храним
        // TODO и передаём всё скрипту
        startScript();
    }

    function setAction( action, action_class, action_text, action_context = false ){
        let element = '<div class="action '+action_class+'" data-action="'+action+'"><span class="go">'+action_text+'</span><span class="reset">Отменить</span></div>';
        if( action_context !== false ){
            action_context.append(element);
            return true;
        }
        return element;
    }
    function processAction ( context, html  ){
        let action_block = '<div class="process-block"><div class="process-content">'+html+'</div><div class="buttons"><div class="button applay">v</div><div class="button break">x</div></div></div>';
        context.toggleClass('process');
        context.append(action_block);
    }
    function finishAction( action_block, id, name  ){

        let html = '<span class="choice" data-id="' + id + '">' + name + '</span>';

        let data_action = action_block.attr('data-action');
        let bind_action = action_block.siblings('.bind[data-action='+data_action+']');


        action_block.find('.process-block').remove();
        action_block.toggleClass('process set');
        action_block.prepend(html);
    }
    function resetAction( context  ){
        let action_block = context.closest('.action');
        action_block.find('.choice').remove();
        action_block.toggleClass('set');
    }

    function propCreate( context, iblock_id, name, code, message ){

        $.ajax({
            method: "POST",
            url: module_path + "ajaxCreateProp.php",
            dataType: 'json',
            context: context,
            data:{ iblock_id:iblock_id, name:name, code:code, force:'N'},
            success: function(data){
                if( data['STATUS'] === 0 ){
                    if( context.find('.message').length > 0){
                        context.find('.message').remove();
                    }
                    context.find('.process-block').prepend('<div class="message">'+data['MESSAGE']+'</div>');
                    if( data['ITEM'] !=='' ){
                        context.find('[name=NAME]').val(data['ITEM']['NAME']);
                        context.find('[name=CODE]').val(data['ITEM']['CODE']);
                    }
                }
                else {
                    finishAction(context, data['ITEM']['ID'],  data['ITEM']['NAME']);
                }

                // propCreate( context, iblock_id, name, code, message );
            },
            error: function(response){
                // $("#result #error").show();
                // setTimeout('$("#result #error").hide()', 5000);
            }
        });
    }

    function addInfo( information, target = false ){
        let container = $('#summ-info');
        if( target === false ) target = container;
        target.append( information );
    }

    function getFile(){

        if( file_path_local == '' ){
            // TODO скачать файл, положить локально и вернуть его адрес
            // TODO получить все настройки связанные с файлом
            file_path_local = file_path_remote;
        }
        return file_path_local;
    }

    function showRemoteFileStatus(){
        // глушим экран
        // проверяем доступность файла
        // Выводим сообщение
        // открывваем экран

        $.ajax({
            method: "POST",
            url: module_path + "getFileStatus.php",
            dataType: 'json',
            data:{file_path:file_path_remote},
            success: function(data){
                $('#file_status').addClass('status_'+data['STATUS']);
                if( data['STATUS'] === 1 ){
                    $('#file_status').text('файл доступен');
                    options_data_for_scritp['SHOP_URL'] = data['url'];
                }
                else if( data['STATUS'] === 0){
                    $('#file_status').text('файл не доступен');

                }
            },
            error: function(response){
                // $("#result #error").show();
                // setTimeout('$("#result #error").hide()', 5000);
            }
        })

    };

    function setFileOptions( current_step ){

        $.ajax({
            method: "POST",
            url: module_path + "getFileOptions.php",
            dataType: 'json',
            context: current_step,
            data:{file_path:file_path_remote},
            success: function(data){
                beforeStepSections(current_step, data);
            },
            error: function(response){
                // $("#result #error").show();
                // setTimeout('$("#result #error").hide()', 5000);
            }
        })

    }

    function getFileStatus(){
        // так как статус получаем в ajax - не можем сразу установить его в переменную
        // Поэтому получаем его на следующем шаге из блока куда его поместил аякс
        if( file_remote_status == '' ){
            // получаем из разметки, если ещё не устаовлен, иначе сразу отдаём установленный
            file_remote_status = 1; // 0 - недоступен, 1 - доступен
        }
        return file_remote_status;
    }

    function remove( context ) {
        context.nextAll('td').detach();
        addLevel( context.closest('tr') );
        context.find('.js_remove').detach();
        container.append('<div class="js_applay">applay</div>');

    }

    function addLevel( context ){
        context.append('<td><a class="js_addlevel">Добавить уровень</a></td>');
    }

    function showShops(){
        $.ajax({
            method: "POST",
            url: module_path + "ajaxGetShops.php",
            dataType: 'json',
            success: function(data){
                $.each(data, function(index,value){
                    $('#shop-file').append('<option value="'+value['FILE_REFERENCE']+'">'+value['NAME']+'</option>');
                });
            },
            error: function(response){
                // $("#result #error").show();
                // setTimeout('$("#result #error").hide()', 5000);
            }
        })
    }

    function showSectionsFromFile(){

        // TODO дополнить данные сохранёнными связями
        if( file_path_local != '' ){
            $.ajax({
                method: "POST",
                url: module_path + "ajaxGetSectionsFromFile.php",
                dataType: 'json',
                // context: context,
                data:{file_path:file_path_local, options:options_data_for_scritp['sections'] },
                success: function(data){
                    // console.log(data['sections']);
                    // TODO отработать статус data['STATUS']
                    // TODO Показать сообщение data['MASSAGE']
                    // TODO визуализировать многоуровневость
                    let container = $('#sections').find('ul');
                    let tmp_death_levl = 1;
                    $.each( data['sections'], function(index,value){
                        if(tmp_death_levl < value['DEPTH_LEVEL']){
                            container = container.find('li').filter( ':last' ).append('<ul class="death-level-' + value['DEPTH_LEVEL'] + '"></ul>').find('ul');
                            tmp_death_levl = value['DEPTH_LEVEL'];
                        }
                        else if( tmp_death_levl > value['DEPTH_LEVEL'] ){
                            container = container.closest('.death-level-' + value['DEPTH_LEVEL']);
                            tmp_death_levl = value['DEPTH_LEVEL'];
                        }
                        container.append('<li class="death-'  + tmp_death_levl + '" data-filesectionid="'+value['ID']+'"><span>' + value['NAME'] + '</span> <div class="bind"><div class="path"></div><div class="show_select"></div><a class="js_addlevel" href="#">+</a></div></li>');
                    });
                    $('#sections').find('ul').siblings('span').addClass('show-sublevel');
                },
                error: function(response){
                    // $("#result #error").show();
                    // setTimeout('$("#result #error").hide()', 5000);
                }
            })
        }
    }

    function showSectionsFromIblock( context, iblock, parrent ){

        $.ajax({
            method: "POST",
            url: module_path + "ajaxGetSectionsFromIblock.php",
            dataType: 'json',
            context: context,
            data:{ iblock:iblock, parrent:parrent },
            success: function(data){
                if( data.length == 0 ){
                    context.closest('.bind').addClass('full').find('.show_select').html('');
                }
                else
                {
                    $.each(data, function(index,value){
                        context.append('<option value="'+value['ID']+'">'+value['NAME']+'</option>');
                    });
                }
            },
            error: function(response){
                // $("#result #error").show();
                // setTimeout('$("#result #error").hide()', 5000);
            }
        })
    }

    function startScript(){
        console.log(getOptions());
        $.ajax({
            method: "POST",
            url: module_path + "startScript.php",
            dataType: 'json',
            // context: context,
            data:{file_path: getFile(), options:getOptions()},
            success: function(data){
                data['obj'];
                startScript();
            },
            error: function(response){
                // $("#result #error").show();
                // setTimeout('$("#result #error").hide()', 5000);
            }
        })
    }
    function saveLinkSection(){
        console.log(options_data_for_scritp);
        $.ajax({
            method: "POST",
            url: module_path + "ajaxSaveCategories.php",
            dataType: 'json',
            // context: context,
            data:{options:options_data_for_scritp},
            success: function(data){
                //data['obj'];
                //startScript();
            },
            error: function(response){
                // $("#result #error").show();
                // setTimeout('$("#result #error").hide()', 5000);
            }
        })
    }
    function getOptions(){
        saveLinkSection();
        // TODO организовать проверку данных перед отдачей ?
        return options_data_for_scritp;
    }

    function setOptions(key, data){

        options_data_for_scritp[key] = data;

        return true;
    }



</script>

<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
// test