<?php

//error_reporting(E_ALL | E_STRICT);
//ini_set('display_errors', 'On');

function removeBOM($text = "") {
    if (substr($text, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
        $text = substr($text, 3);
    }
    return $text;
}

function sendrequest($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 0);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

$strJSON = removeBOM(sendrequest('http://1c-etp.ru/export/tariffs?requestType=getInfo'));
$tariffs = json_decode($strJSON);
//var_dump($strJSON);
//var_dump($tariffs);
//echo json_last_error();
?>

<style>
    .tariffs_list {display: block; margin: 20px 0; padding: 0; list-style: none; text-align: center;}
    .tariffs_list:before {font-size: 110%; color: grey; content: 'Выберите интересующий вас тариф:'; display: block; padding: 30px;}
    .tariffs_list .tariff {display: block; box-sizing: border-box; float: left; width: 14.28%; border-top: 2px solid; border-bottom: 2px solid; padding: 20px 0; border-color: #016E3A; cursor: pointer; transition: all 0.2s;}
    .tariffs_list .tariff:nth-of-type(1) {border-left: 2px solid #016E3A;}
    .tariffs_list .tariff:nth-last-of-type(1) {border-right: 2px solid #016E3A;}
    .tariffs_list .tariff.active {transform: scale(1.1,1.1); background-color: #016E3A; border-color: transparent; box-shadow: 0 2px 4px rgba(0,0,0,0.4);}
    .tariffs_list .tariff > span {display: block;}
    .tariffs_list .tariff.active > span {color: #fff !important; border-color: transparent !important;}
    .tariffs_list .tariff .title {font-weight: 500; text-transform: uppercase; padding: 20px 10px; color: #444; font-size: 90%;}
    .tariffs_list .tariff .short_desc {font-size: 80%; color: silver; min-height: 130px; border-right: 1px solid #016E3A; padding: 0 20px; word-break: break-all;}
    .tariffs_list .tariff .price {padding: 20px 10px; border-right: 1px solid #016E3A; color: #016E3A;}
    .tariffs_list .tariff.active .price {background-color: rgba(255,255,255,0.2);}
    .tariffs_list .tariff .price .price_value {font-size: 180%;}
    .tariffs_list .tariff:nth-last-of-type(1) > span {border: none;}

    .tariff_info_container {overflow: hidden; padding: 20px 0; border: 1px solid transparent;}

    .tariff_info_container .close {display: none;}

    .tariff_info_container .full_desc {width: 50%; float: left;}
    .tariff_info_container .full_desc .desc_text {font-size: 105%; color: #444; line-height: 150%; padding-right: 10px;}

    .tariff_info_container .order_components {width: 50%; float: right; max-width: 550px;}
    .tariff_info_container .order_components .order_component {overflow: display; padding: 20px 0;}
    .tariff_info_container .order_components .order_component .input_container {display: inline-block; float: left; max-width: 80%;}

    .ext_desc {font-size: 120%; color: silver; cursor: pointer; position: relative; margin-left: 5px;}
    .ext_desc .tooltip {position: absolute; top: -110px; left: -125px; width: 250px; min-height: 100px; padding: 20px; box-shadow: 0 2px 7px rgba(0,0,0,0.4); z-index: 20; background-color: #fff; font-family: 'Roboto'; font-weight: 400; font-size: 70%; color: #000; border-radius: 4px; line-height: 120%;}

    .tariff_info_container .order_components .order_component .input_container [type="text"] {padding: 3px; border: 1px solid silver; border-radius: 3px; text-align: right; max-width: 30px; margin-left: 5px;}
    .tariff_info_container .order_components .order_component .price_container {display: inline-block; float: right; max-width: 20%; color: #016E3A;}
    .tariff_info_container .order_components .order_component .price_container .price_value {font-size: 120%; margin-right: 5px;}
    .tariff_info_container .order_components .order_total {display: block; padding: 25px; margin: 10px 0; background-color: #FFFFFF; font-size: 140%; border-radius: 4px; overflow: hidden; color: #016E3A; text-align: right;}
    .tariff_info_container .order_components .order_total .value {font-size: 115%; margin-right: 3px;}
    .tariff_info_container .order_components .order_total:before {display: inline-block; float: left; content: 'Итого:'; color: #777;}
    .tariff_info_container .order_components .place_order_button {display: block; box-sizing: border-box; padding: 25px; background-color: #016E3A; width: 100%; text-transform: uppercase; font-size: 120%; color: #fff; font-weight: 500; outline: none; border: 1px solid transparent; border-radius: 4px; cursor: pointer; transition: background-color 0.2s;}
    .tariff_info_container .order_components .place_order_button:hover {background-color: #00542C;}

    .tariff_info_container .emarkets_list_container {width: 100%; float: left;}
    .tariff_info_container .emarkets_list_container .emarkets_groups_list {margin: 20px 0; padding: 0; list-style: none; cursor: default;}
    .tariff_info_container .emarkets_list_container .emarkets_groups_list li {display: inline-block; padding: 10px 15px; border-radius: 4px; margin: 0 10px 10px 0; background-color: #96ABA1; color: #fff; cursor: pointer; transition: all 0.15s;}
    .tariff_info_container .emarkets_list_container .emarkets_groups_list li:hover {background-color: #016E3A; color: #fff;}
    .tariff_info_container .emarkets_list_container .emarkets_groups_list li.active {background-color: #016E3A; color: #fff;}
    .tariff_info_container .emarkets_list_container .actual_emarkets_list {margin: 20px 0; padding: 0; list-style: none;}
    .tariff_info_container .emarkets_list_container .actual_emarkets_list li {display: inline-block; width: 25%; margin-bottom: 10px; padding: 0 10px; vertical-align: top; font-size: 90%;}

</style>

<h1>Тарифы</h1>
<ul class="tariffs_list">
<?php foreach ($tariffs as $tariff) { ?>
        <li id="tariff<?php echo $tariff->id ?>" class="tariff <?php echo ($tariff->id == 4) ? 'active' : '' ?>" data-id="<?php echo $tariff->id ?>" data-price="<?php echo $tariff->price ?>" title="Просмотреть информацию о тарифе">
            <span class="title"><?php echo $tariff->title ?></span>
            <span class="short_desc"><?php echo $tariff->shortDesc ?></span>
            <span class="price">
                <span class="price_value"><?php echo $tariff->price ?></span>
                р/год
            </span>
        </li>
<?php } ?>
</ul>
<?php foreach ($tariffs as $tariff) { ?>
    <div class="tariff_info_container" id="tariff_info<?php echo $tariff->id ?>" data-id="<?php echo $tariff->id ?>" style="display: none">
        <div class="full_desc">
            <h2 style="margin-top: 50px;"><?php echo $tariff->title ?></h2>
            <div class="desc_text">
                <p>Тариф включает в себя:</p>
                <p><?php echo $tariff->fullDesc ?></p>
            </div>
        </div>
        <div class="order_components">
            <?php
            if (count($tariff->extensions) > 0) {
                echo "<h2 style='margin-top: 50px;'>Дополнительные расширения:</h2>";
            }
            $i = 0;
            foreach ($tariff->extensions as $extension) {
                ?>
                <div class="order_component">
                    <span class="input_container">
                        <input type="checkbox" class="extension extensionFor<?php echo $tariff->id ?>" value="0" id="ext_<?php echo $tariff->id . '_' . $i ?>" data-tariff-id="<?php echo $tariff->id ?>" data-price="<?php echo $extension->price ?>">
                        <label for="ext_<?php echo $tariff->id . '_' . $i ?>"><?php echo $extension->name_extended ?>
                        </label>
                    </span>
                    <span class="price_container">
                        <span class="price_value"><?php echo $extension->price ?></span>р/год
                    </span>
                </div>    
                <?php
                $i +=1;
            }
            ?>
            <h2 style="margin-top: 50px;">Дополнительные услуги:</h2>
            <?php
            $i = 0;
            foreach ($tariff->tokens as $token) {
                ?>
                <div class="order_component">
                    <span class="input_container">
                        <input type="checkbox" class="token tokenFor<?php echo $tariff->id ?>" value="0" id="token_<?php echo $tariff->id . '_' . $i ?>" data-token-id="<?php echo $i ?>" data-tariff-id="<?php echo $tariff->id ?>" data-price="<?php echo $token->price ?>">
                        <label for="token_<?php echo $tariff->id . '_' . $i ?>"><?php echo $token->name ?></label>
                        <input type="text" value="1" class="tokens_quantity" size="3" pattern="[0-9]{3}" maxlength="3" title="Количество носителей" id="tokens_quantity_<?php echo $tariff->id . '_' . $i ?>" data-price="<?php echo $token->price ?>" data-tariff-id="<?php echo $tariff->id ?>">
                    </span>
                    <span class="price_container">
                        <span class="price_value"><?php echo $token->price ?></span>р/год
                    </span>
                </div>                    
                <?php
                $i +=1;
            }
            ?>
            <div id="order_total" class="order_total"><span id="order_total_<?php echo $tariff->id ?>" class="value"><?php echo $tariff->price ?></span>руб</div>
            <button class="place_order_button" id="invoke_order_form" title="Оформить заказ">Заказать</button>
        </div>
        <?php
        $strJSON1 = removeBOM(sendrequest('http://1c-etp.ru/export/emarkets?requestType=tariffSpecific&tariffId=' . $tariff->id));
        $emarkets = json_decode($strJSON1);
        //var_dump($emarkets);
        if ($emarkets != null) {
            ?>
            <div class="emarkets_list_container">
                <h2>Принимается на <?php echo count($emarkets) ?> площадках</h2>
                <ul class="emarkets_groups_list">
                    <?php
                    $arrEmarketsGroup = array();
                    foreach ($emarkets as $emarket) {
                        foreach ($emarket->groups_titles as $group_title) {
                            if (!in_array($group_title, $arrEmarketsGroup)) {
                                $arrEmarketsGroup[] = $group_title;
                                $emarketGroupId = array_search($group_title, $arrEmarketsGroup);
                                ?>
                                <li id="emarket_group<?php echo $tariff->id . '_' . $emarketGroupId ?>" data-tariff-id="<?php echo $tariff->id ?>" data-emarket-group-id="<?php echo $emarketGroupId ?>" class="emarket_groups emarket_group<?php echo $tariff->id ?> <?php echo ($emarketGroupId === 0) ? 'active' : ''; ?>">
                                <?php echo $group_title ?>
                                </li>            
                <?php } ?>
            <?php }
        } ?>

                </ul>
                <ul class="actual_emarkets_list">
                    <?php
                    foreach ($emarkets as $emarket) {
                        $emarketTitle = $emarket->title;
                        foreach ($emarket->groups_titles as $group_title) {
                            $emarketGroupId = array_search($group_title, $arrEmarketsGroup);
                            ?>
                            <li id="emarket_<?php echo $tariff->id . '_' . $emarketGroupId ?>" data-tariff-id="<?php echo $tariff->id ?>" data-emarket-group-id="<?php echo $emarketGroupId ?>" class="emarket emarket_<?php echo $tariff->id ?> emarket_<?php echo $tariff->id . '_' . $emarketGroupId ?>">
                <?php echo $emarketTitle ?>
                            </li>            
                <?php }
            } ?>

                </ul>
            </div> 
    <?php } ?>
    </div>
<?php } ?>
<script type="text/javascript">
    function setVisibility(id, gid) {
        $(".tariff").removeClass('active');
        $(".tariff_info_container").css('display', 'none');
        $("#tariff" + id).addClass('active');
        $("#tariff_info" + id).css('display', 'table-row');
        $(".emarket_group" + id).removeClass('active');
        $("#emarket_group" + id + "_" + gid).addClass('active');
        $(".emarket_" + id).css('display', 'none');
        $(".emarket_" + id + "_" + gid).css('display', 'inline-block');
    }
    function totalSumTariffTokens(id) {
        var total = 0;
        $(".tokenFor" + id).each(function (indx, element) {
            //heights.push($(element).height());
            price = element.getAttribute("data-price");
            count = $("#tokens_quantity_" + id + '_' + element.getAttribute("data-token-id")).val();
            checked = element.checked;
            total += price * count * checked;
        });
        return total;
    }
    function totalSumTariffExtension(id) {
        var total = 0;
        $(".extensionFor" + id).each(function (indx, element) {
            //heights.push($(element).height());
            price = element.getAttribute("data-price");
            checked = element.checked;
            total += price * checked;
        });
        return total;
    }
    function totalSumTariff(id) {
        var total = 0;
        tariffPrice = $("#tariff" + id).data("price");
        total += totalSumTariffTokens(id);
        total += totalSumTariffExtension(id);
        total += tariffPrice;
        $("#order_total_" + id).text(total);
        return total;
    }
    $(document).ready(function () {
        setVisibility(4, 0);
        $(".tariff").click(function () {
            setVisibility(this.getAttribute("data-id"),0);
        });
        $(".token").click(function () {
            totalSumTariff(this.getAttribute("data-tariff-id"));
        });
        $(".extension").click(function () {
            totalSumTariff(this.getAttribute("data-tariff-id"));
        });
        $(".tokens_quantity").change(function () {
            totalSumTariff(this.getAttribute("data-tariff-id"));
        });
        $(".emarket_groups").click(function () {
            setVisibility(this.getAttribute("data-tariff-id"), this.getAttribute("data-emarket-group-id"));
        });
    });
</script>        
