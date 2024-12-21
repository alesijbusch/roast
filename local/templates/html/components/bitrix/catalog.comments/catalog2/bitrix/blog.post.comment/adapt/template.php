<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @global CMain $APPLICATION */
CJSCore::Init(array("image"));
?>
    <div id="reviews_sort_continer"></div>
    <div class="blog-comments" id="blg-comment-<?= $arParams["ID"] ?>" data-rating="<?=$arResult['ALL_RATING_VALUE']?>" data-count="<?=$arResult['REVIEWS_COUNT'] ?? '0'?>">
        <a name="comments"></a>
        <?
        if ($arResult["is_ajax_post"] != "Y") {
            include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/script.php");
        } else {
            $APPLICATION->RestartBuffer();
            ?>
            <script>window.BX = top.BX;
                <?if($arResult["use_captcha"] === true)
                {
                ?>
                var cc;
                if (document.cookie.indexOf('<?echo session_name()?>' + '=') == -1)
                    cc = Math.random();
                else
                    cc = '<?=$arResult["CaptchaCode"]?>';

                BX('captcha').src = '/bitrix/tools/captcha.php?captcha_code=' + cc;
                BX('captcha_code').value = cc;
                if (BX('captcha_word') !== null) {
                    BX('captcha_word').value = "";
                }
                <?
                }
                ?>
                if (!top.arImages)
                    top.arImages = [];
                if (!top.arImagesId)
                    top.arImagesId = [];
                <?
                if($arResult["Images"]) {
                foreach($arResult["Images"] as $aImg)
                {
                ?>
                top.arImages.push('<?=CUtil::JSEscape($aImg["SRC"])?>');
                top.arImagesId.push('<?=$aImg["ID"]?>');
                <?
                }
                }
                ?>
            </script><?
        if (strlen($arResult["COMMENT_ERROR"]) > 0)
        {
        ?>
            <script>top.commentEr = 'Y';</script>
            <div class="alert alert-danger blog-note-box blog-note-error">
                <div class="blog-error-text">
                    <?= $arResult["COMMENT_ERROR"] ?>
                </div>
            </div>
            <?
        }
        }

        if (strlen($arResult["MESSAGE"]) > 0) {
            ?>
            <div class="blog-textinfo blog-note-box">
                <div class="blog-textinfo-text">
                    <?= $arResult["MESSAGE"] ?>
                </div>
            </div>
            <?
        }
        if (strlen($arResult["ERROR_MESSAGE"]) > 0) {
            ?>
            <div class="alert alert-danger blog-note-box blog-note-error">
                <div class="blog-error-text" id="blg-com-err">
                    <?= $arResult["ERROR_MESSAGE"] ?>
                </div>
            </div>
            <?
        }
        if (strlen($arResult["FATAL_MESSAGE"]) > 0)
        {
            ?>
            <div class="alert alert-danger blog-note-box blog-note-error">
                <div class="blog-error-text">
                    <?= $arResult["FATAL_MESSAGE"] ?>
                </div>
            </div>
            <?
        }
        else
        {
        if ($arResult["imageUploadFrame"] == "Y")
        {
            ?>
            <script>
                <?if(!empty($arResult["Image"])):?>
                top.bxBlogImageId = top.arImagesId.push('<?=$arResult["Image"]["ID"]?>');
                top.arImages.push('<?=CUtil::JSEscape($arResult["Image"]["SRC"])?>');
                top.bxBlogImageIdWidth = '<?=CUtil::JSEscape($arResult["Image"]["WIDTH"])?>';
                <?elseif(strlen($arResult["ERROR_MESSAGE"]) > 0):?>
                top.bxBlogImageError = '<?=CUtil::JSEscape($arResult["ERROR_MESSAGE"])?>';
                <?endif;?>
            </script>
            <?
            die();
        }
        else
        {
        if ($arResult["is_ajax_post"] != "Y" && $arResult["CanUserComment"]) {
            /*$ajaxPath = POST_FORM_ACTION_URI;
            $parent = $component->GetParent();
            if (isset($parent) && is_object($parent))
            {
                $ajaxPath = $parent->GetTemplate()->GetFolder().'/ajax.php';
            }*/
            $ajaxPath = $templateFolder . '/ajax.php';
            ?>

            <!--<div id="form_comment_">
                <div id="form_c_del">
                    <div class="blog-comment-form rounded3 bordered">
                        <form enctype="multipart/form-data" method="POST" name="form_comment" id="form_comment"
                              action="<?= $ajaxPath; ?>">
                            <input type="hidden" name="parentId" id="parentId" value="">
                            <input type="hidden" name="edit_id" id="edit_id" value="">
                            <input type="hidden" name="act" id="act" value="add">
                            <input type="hidden" name="post" value="Y">
                            <?
                            if (isset($_REQUEST["IBLOCK_ID"])) {
                                ?><input type="hidden" name="IBLOCK_ID" value="<?= (int)$_REQUEST["IBLOCK_ID"]; ?>"><?
                            }
                            if (isset($_REQUEST["ELEMENT_ID"])) {
                                ?><input type="hidden" name="ELEMENT_ID" value="<?= (int)$_REQUEST["ELEMENT_ID"]; ?>"><?
                            }
                            if (isset($_REQUEST["SITE_ID"])) {
                                ?><input type="hidden" name="SITE_ID"
                                         value="<?= htmlspecialcharsbx($_REQUEST["SITE_ID"]); ?>"><?
                            }

                            echo makeInputsFromParams($arParams["PARENT_PARAMS"]);
                            echo bitrix_sessid_post(); ?>
                            <div class="form blog-comment-fields">
                                <?
                                if (empty($arResult["User"])) {
                                    ?>
                                    <div class="blog-comment-field blog-comment-field-user">
                                        <div class="row form">
                                            <div class="col-md-6 col-sm-6">
                                                <div class="form-group animated-labels <?= ($_SESSION["blog_user_name"] ? 'input-filed' : ''); ?>">
                                                    <label for="user_name"><?= GetMessage("B_B_MS_NAME") ?> <span
                                                                class="required-star">*</span></label>
                                                    <div class="input">
                                                        <input maxlength="255" size="30" class="form-control"
                                                               tabindex="3" type="text" name="user_name" id="user_name"
                                                               value="<?= htmlspecialcharsEx(
                                                                   $_SESSION["blog_user_name"]
                                                               ) ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <div class="form-group animated-labels <?= ($_SESSION["blog_user_email"] ? 'input-filed' : ''); ?>">
                                                    <label for="user_email">E-mail</label>
                                                    <div class="input">
                                                        <input maxlength="255" size="30" class="form-control"
                                                               tabindex="4" type="text" name="user_email"
                                                               id="user_email" value="<?= htmlspecialcharsEx(
                                                            $_SESSION["blog_user_email"]
                                                        ) ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?
                                }
                                ?>
                                <? if ($arParams["NOT_USE_COMMENT_TITLE"] != "Y") {
                                    ?>
                                    <div class="row form">
                                        <div class="col-md-12">
                                            <div class="form-group animated-labels">
                                                <label for="user_sbj"><?= GetMessage("BPC_SUBJECT") ?></label>
                                                <div class="input">
                                                    <input maxlength="255" size="70" class="form-control" tabindex="3"
                                                           type="text" name="subject" id="user_sbj" value="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?
                                } ?>

                                <label class="rating_label"><?= GetMessage("BPC_RATING") ?></label>
                                <div class="votes_block nstar big with-text">
                                    <div class="ratings">
                                        <div class="inner_rating">
                                            <? for ($i = 1; $i <= 5; $i++):?>
                                                <div class="item-rating" data-message="<?= GetMessage(
                                                    'RATING_MESSAGE_' . $i
                                                ) ?>">
                                                    <i class="svg inline  svg-inline-star" aria-hidden="true">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="27" height="24" viewBox="0 0 27 24">
                                                            <defs>
                                                                <style>.cls-1{fill:#d4d4d4;fill-rule:evenodd;}</style>
                                                            </defs>
                                                            <path
                                                                data-name="Shape 921 copy 8"
                                                                class="cls-1"
                                                                d="M461.929,5200H472l-7.845,5.88L467,5215l-8.481-4.86L450,5215l2.79-9.13h0L445,5200h10.055l3.464-8.99Z"
                                                                transform="translate(-445 -5191)">
                                                            </path>
                                                        </svg>
                                                    </i>
                                                </div>
                                            <?endfor; ?>
                                        </div>
                                    </div>
                                    <div class="rating_message muted"
                                         data-message="<?= GetMessage('RATING_MESSAGE_0') ?>"><?= GetMessage(
                                            'RATING_MESSAGE_0'
                                        ) ?></div>
                                    <input class="hidden" name="rating">
                                </div>

                                <div class="row form virtues">
                                    <div class="col-md-12">
                                        <div class="form-group animated-labels">
                                            <label for="virtues"><?= GetMessage("BPC_VIRTUES") ?></label>
                                            <div class="input">
                                                <textarea rows="3" class="form-control" tabindex="3" name="virtues"
                                                          id="virtues" value=""></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row form limitations">
                                    <div class="col-md-12">
                                        <div class="form-group animated-labels">
                                            <label for="limitations"><?= GetMessage("BPC_LIMITATIONS") ?></label>
                                            <div class="input">
                                                <textarea rows="3" class="form-control" tabindex="3" name="limitations"
                                                          id="limitations" value=""></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row form comment">
                                    <div class="col-md-12">
                                        <div class="form-group animated-labels">
                                            <label for="comment"><?= GetMessage("BPC_MESSAGE") ?></label>
                                            <div class="input">
                                                <textarea rows="3" class="form-control" tabindex="3" name="comment"
                                                          id="comment" value=""></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row form files">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="input">
                                                <input type="file" multiple class="form-control" tabindex="3"
                                                       name="comment_images[]" id="comment_images" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?
                                include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lhe.php");

                                
                                if ($arResult["COMMENT_PROPERTIES"]["SHOW"] == "Y") {
                                    ?><br/><?
                                    $eventHandlerID = false;
                                    $eventHandlerID = AddEventHandler(
                                        'main',
                                        'system.field.edit.file',
                                        array('CBlogTools', 'blogUFfileEdit')
                                    );
                                    foreach ($arResult["COMMENT_PROPERTIES"]["DATA"] as $FIELD_NAME => $arPostField) {
                                        if ($FIELD_NAME == 'UF_BLOG_COMMENT_DOC') {
                                            ?><a id="blog-upload-file" href="javascript:blogShowFile()"><?= GetMessage(
                                            "BLOG_ADD_FILES"
                                        ) ?></a><?
                                        }
                                        ?>
                                        <div
                                        id="blog-comment-user-fields-<?= $FIELD_NAME ?>"><?= ($FIELD_NAME == 'UF_BLOG_COMMENT_DOC' ? "" : $arPostField["EDIT_FORM_LABEL"] . ":") ?>
                                        <? $APPLICATION->IncludeComponent(
                                            "bitrix:system.field.edit",
                                            $arPostField["USER_TYPE"]["USER_TYPE_ID"],
                                            array("arUserField" => $arPostField),
                                            null,
                                            array("HIDE_ICONS" => "Y")
                                        ); ?>
                                        </div><?
                                    }
                                    if ($eventHandlerID !== false && (intval($eventHandlerID) > 0)) {
                                        RemoveEventHandler('main', 'system.field.edit.file', $eventHandlerID);
                                    }
                                }

                                if (strlen($arResult["NoCommentReason"]) > 0) {
                                    ?>
                                    <div id="nocommentreason"
                                         style="display:none;"><?= $arResult["NoCommentReason"] ?></div>
                                    <?
                                }
                                if ($arResult["use_captcha"] === true) {
                                    ?>
                                    <div class="row captcha-row form">
                                        <div class="col-md-8 col-sm-8 col-xs-8">
                                            <div class="form-group animated-labels">
                                                <label for="captcha_word"><?= GetMessage("B_B_MS_CAPTCHA_SYM") ?> <span
                                                            class="required-star">*</span></label>
                                                <div class="input">
                                                    <input type="hidden" name="captcha_code" id="captcha_code"
                                                           class="captcha_sid" value="<?= $arResult["CaptchaCode"] ?>">
                                                    <input type="text" size="30" name="captcha_word"
                                                           class="form-control" id="captcha_word" value="" tabindex="7">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-4">
                                            <div class="form-group">
                                                <div class="captcha-img">
                                                    <div class="blog-comment-field-captcha-image">
                                                        <div id="div_captcha"></div>
                                                    </div>
                                                    <span class="refresh captcha_reload"><a href="javascript:;"
                                                                                            rel="nofollow"><?= GetMessage(
                                                                "REFRESH"
                                                            ) ?></a></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?
                                }
                                ?>

                                <div class="blog-comment-buttons-wrapper">
                                    <input tabindex="10" class="btn btn-default btn-lg"
                                           value="<?= GetMessage("B_B_MS_SEND") ?>" type="button" name="sub-post"
                                           id="post-button" onclick="submitComment()">
                                </div>
                            </div>
                            <input type="hidden" name="blog_upload_cid" id="upload-cid" value="">
                        </form>
                    </div>
                </div>
            </div> -->

            <div class="modal fade modal-comment modal-wide" id="modal-comment" tabindex="-1" aria-modal="true" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                      <button class="modal-close" type="button" data-dismiss="modal" aria-label="Close">
                        <svg class="icon" style="width: 14px; height: 14px;">
                          <use xlink:href="<?=ASSET_TEMPLATE_PATH?>/images/sprite.svg#i-close"></use>
                        </svg>
                      </button>
                      <div class="modal-body">
                        <div x-data>
                          <div class="comment-add-form" x-show="!$store.formComments.success">
                            <div class="modal-title">Оставить отзыв</div>
                            <form class="comment-form" enctype="multipart/form-data" method="POST" name="form_comment" id="form_comment" action="<?= $ajaxPath; ?>"><!--Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/components-template/form-mixin/style.css");-->
                                <input type="hidden" name="parentId" id="parentId" value="">
                                <input type="hidden" name="edit_id" id="edit_id" value="">
                                <input type="hidden" name="act" id="act" value="add">
                                <input type="hidden" name="post" value="Y">
                                <?
                                if (isset($_REQUEST["IBLOCK_ID"])) {
                                    ?><input type="hidden" name="IBLOCK_ID" value="<?= (int)$_REQUEST["IBLOCK_ID"]; ?>"><?
                                }
                                if (isset($_REQUEST["ELEMENT_ID"])) {
                                    ?><input type="hidden" name="ELEMENT_ID" value="<?= (int)$_REQUEST["ELEMENT_ID"]; ?>"><?
                                }
                                if (isset($_REQUEST["SITE_ID"])) {
                                    ?><input type="hidden" name="SITE_ID"
                                            value="<?= htmlspecialcharsbx($_REQUEST["SITE_ID"]); ?>"><?
                                }

                                echo makeInputsFromParams($arParams["PARENT_PARAMS"]);
                                echo bitrix_sessid_post(); ?>
                                <? if (empty($arResult["User"])) { ?>
                                    <div class="form-group">
                                        <label class="form-control-label" for="user_name">Ваше имя<span class="label-required">*</span> <span class="error hidden" style="color:red"></span>
                                        </label>
                                        <input 
                                            class="form-control"
                                            type="text"
                                            name="user_name"
                                            id="user_name"
                                            value="<?= htmlspecialcharsEx($_SESSION["blog_user_name"]) ?>"
                                            placeholder=""
                                            required="required"
                                        />
                                    </div><!--Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/components-template/form-mixin/style.css");-->
                                    <div class="form-group">
                                        <label class="form-control-label" for="user_email">E-mail <span class="error hidden" style="color:red"></span>
                                        </label>
                                        <input 
                                            class="form-control"
                                            type="text"
                                            name="user_email"
                                            id="user_email"
                                            value="<?= htmlspecialcharsEx($_SESSION["blog_user_email"]) ?>" 
                                            placeholder=""
                                        />
                                    </div>
                                <? } ?>
                              <div class="form-group comment-form__full">
                                <label class="form-control-label" for="rating">Ваша оценка<span class="label-required">*</span> <span class="error hidden" style="color:red"></span></label>
                                <div class="rate-group-wrap"><!--Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/components-template/form-mixin/style.css");--><!--Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/components-template/form-mixin/script.min.js");-->
                                  <div class="rate-group">
                                    <input class="radio-rate" type="radio" name="rating" value="5" id="rate-5" data-mess="Отлично" required/>
                                    <label class="radio-rate-label" for="rate-5"></label>
                                    <input class="radio-rate" type="radio" name="rating" value="4" id="rate-4" data-mess="Хорошо" required/>
                                    <label class="radio-rate-label" for="rate-4"></label>
                                    <input class="radio-rate" type="radio" name="rating" value="3" id="rate-3" data-mess="Нормально" required/>
                                    <label class="radio-rate-label" for="rate-3"></label>
                                    <input class="radio-rate" type="radio" name="rating" value="2" id="rate-2" data-mess="Плохо" required/>
                                    <label class="radio-rate-label" for="rate-2"></label>
                                    <input class="radio-rate" type="radio" name="rating" value="1" id="rate-1" data-mess="Очень плохо" required/>
                                    <label class="radio-rate-label" for="rate-1"></label>
                                  </div>
                                  <div class="rate-group-wrap__text">Хорошо</div>
                                </div>
                              </div><!--Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/components-template/form-mixin/style.css");-->
                              <div class="form-group">
                                <label class="form-control-label" for="virtues">Достоинства</label>
                                <textarea class="form-control" rows="3" name="virtues" id="virtues" value="" placeholder=""></textarea>
                              </div><!--Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/components-template/form-mixin/style.css");-->
                              <div class="form-group">
                                <label class="form-control-label" for="limitations">Недостатки</label>
                                <textarea class="form-control" rows="3" name="limitations" id="limitations" value="" placeholder=""></textarea>
                              </div><!--Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/components-template/form-mixin/style.css");-->
                              <div class="form-group comment-form__full">
                                <label class="form-control-label" for="comment">Комментарий<span class="label-required">*</span> <span class="error hidden" style="color:red"></span></label>
                                <textarea class="form-control" rows="3" name="comment" id="comment" value="" placeholder="" required="required"></textarea>
                                <input type="hidden" name="comment">
                              </div>
                              <div class="comment-form__bottom comment-form__full">
                                <div class="policy-block"><!--Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/components-template/form-mixin/style.css");-->
                                  <div class="form-group">
                                    <div class="checkbox">
                                      <input type="checkbox" id="policy" value="" name="policy" required="required" checked="checked"/>
                                      <label for="policy">Я согласен на <a href="/info/licenses_detail/"> обработку персональных данных</a><span class="label-required">*</span> <span class="error hidden" style="color:red"></span>
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <button class="btn" name="sub-post" id="post-button">Оставить отзыв</button>
                              </div>
                            </form>
                          </div>
                          <div class="comment-form__success" x-show="$store.formComments.success">
                            <div class="comment-form__success-image"><svg xmlns="http://www.w3.org/2000/svg" width="62" height="59" viewBox="0 0 62 59" fill="none">
<path d="M53.0284 18.9453L48.4172 1.72327C48.3525 1.48134 48.2407 1.25454 48.0884 1.05582C47.936 0.857097 47.7459 0.690349 47.529 0.565103C47.3122 0.439857 47.0728 0.358567 46.8245 0.325879C46.5762 0.293191 46.3239 0.309745 46.082 0.374596L43.4876 1.07038C43.2671 1.12783 43.0589 1.22471 42.8729 1.35631L37.2895 5.34896L32.4666 1.21621C31.9456 0.768828 31.2815 0.522864 30.5947 0.522864C29.9079 0.522864 29.2439 0.768828 28.7228 1.21621L2.9884 23.3954C2.67729 23.6646 2.42793 23.9978 2.25735 24.3722C2.08678 24.7465 1.999 25.1533 2.00001 25.5647V54.2347C2.00001 54.9931 2.30126 55.7204 2.8375 56.2566C3.37374 56.7929 4.10103 57.0941 4.85938 57.0941H56.3281C57.0865 57.0941 57.8138 56.7929 58.35 56.2566C58.8862 55.7204 59.1875 54.9931 59.1875 54.2347V25.5647C59.1887 25.1529 59.1009 24.7457 58.9301 24.371C58.7594 23.9963 58.5097 23.6628 58.1982 23.3935L53.0284 18.9453ZM57.2612 54.3358L39.8648 40.4144L57.2813 26.4769V54.2347C57.2765 54.2688 57.2698 54.3025 57.2612 54.3358ZM3.90626 54.2347V26.4759L21.3275 40.4144L3.92627 54.3358C3.91758 54.3025 3.9109 54.2688 3.90626 54.2347ZM29.9952 35.9185C30.1657 35.7852 30.3759 35.7128 30.5923 35.7128C30.8088 35.7128 31.019 35.7852 31.1895 35.9185L55.274 55.1879H5.91259L29.9952 35.9185ZM46.5729 2.21603L51.5063 20.6295L49.6648 21.1222L44.7315 2.70975L46.5729 2.21603ZM29.6406 12.6785L31.6136 20.0442L26.0855 21.5244C25.1081 21.7861 24.0668 21.6487 23.1907 21.1426C22.3145 20.6365 21.6754 19.8031 21.4137 18.8257C21.1521 17.8483 21.2895 16.807 21.7956 15.9309C22.3017 15.0547 23.1351 14.4156 24.1125 14.1539L29.6406 12.6785ZM31.1866 22.1316L32.9137 28.5766C32.9789 28.8207 32.9445 29.0806 32.8182 29.2994C32.6918 29.5181 32.4838 29.6778 32.2398 29.7432C31.9957 29.8085 31.7358 29.7741 31.517 29.6478C31.2983 29.5214 31.1386 29.3134 31.0732 29.0694L29.3461 22.6253L31.1866 22.1316ZM33.5313 19.8326L31.4068 11.9026L42.9996 3.61046L47.7147 21.208L33.5313 19.8326ZM29.9695 2.65733C30.143 2.50608 30.3654 2.42276 30.5957 2.42276C30.8259 2.42276 31.0483 2.50608 31.2219 2.65733L35.692 6.48794L29.8989 10.6283L23.8466 12.2486C22.4321 12.6041 21.2004 13.473 20.391 14.6862C19.5815 15.8994 19.2522 17.3703 19.467 18.8128C19.6031 19.5953 19.9007 20.341 20.3409 21.0021C20.781 21.6633 21.3541 22.2255 22.0236 22.653C22.6931 23.0804 23.4442 23.3637 24.2292 23.4848C25.0142 23.6059 25.8158 23.5622 26.583 23.3563L27.5037 23.1104L29.2051 29.4611C29.3514 30.0519 29.6747 30.5839 30.1315 30.9861C30.4847 31.2876 30.906 31.4986 31.3591 31.6007C31.8121 31.7028 32.2832 31.6931 32.7316 31.5723C33.1812 31.4525 33.5947 31.2249 33.9363 30.9092C34.278 30.5934 34.5374 30.1991 34.6922 29.7604C34.8837 29.1837 34.8954 28.5624 34.7256 27.979L33.0433 21.696L48.726 23.221C48.9534 23.2438 49.183 23.225 49.4037 23.1657L52 22.4699C52.2433 22.4051 52.4712 22.2924 52.6703 22.1384C52.8695 21.9844 53.036 21.7922 53.1599 21.573L56.6284 24.5573L38.3398 39.1963L32.378 34.4259C31.8711 34.0206 31.2414 33.7998 30.5923 33.7998C29.9433 33.7998 29.3136 34.0206 28.8066 34.4259L22.8525 39.1916L4.5582 24.5573L29.9695 2.65733Z" fill="#8A6048"/>
<path d="M52.7198 10.4356C52.774 10.6384 52.8936 10.8177 53.0601 10.9456C53.2266 11.0735 53.4306 11.1428 53.6405 11.1428C53.7239 11.1429 53.807 11.1316 53.8874 11.1095L57.5703 10.123C57.8145 10.0575 58.0226 9.89772 58.149 9.67875C58.2754 9.45979 58.3096 9.19959 58.2441 8.95541C58.1787 8.71122 58.0189 8.50303 57.7999 8.37666C57.5809 8.25029 57.3207 8.21607 57.0765 8.28154L53.3937 9.26898C53.1497 9.33444 52.9416 9.49408 52.8153 9.71283C52.6889 9.93158 52.6546 10.1915 52.7198 10.4356Z" fill="#8A6048"/>
<path d="M51.6014 5.76867C51.7267 5.76877 51.8508 5.74416 51.9666 5.69624C52.0824 5.64832 52.1876 5.57803 52.2762 5.48941L54.9716 2.79397C55.0626 2.70605 55.1353 2.60087 55.1852 2.48459C55.2352 2.3683 55.2615 2.24324 55.2626 2.11668C55.2637 1.99013 55.2395 1.86462 55.1916 1.74748C55.1437 1.63035 55.0729 1.52393 54.9834 1.43444C54.8939 1.34495 54.7875 1.27417 54.6704 1.22625C54.5532 1.17833 54.4277 1.15421 54.3012 1.15531C54.1746 1.15641 54.0496 1.1827 53.9333 1.23265C53.817 1.28261 53.7118 1.35522 53.6239 1.44625L50.9275 4.14169C50.7942 4.27498 50.7035 4.4448 50.6667 4.62966C50.63 4.81452 50.6489 5.00613 50.721 5.18027C50.7931 5.3544 50.9152 5.50324 51.0719 5.60798C51.2286 5.71271 51.4129 5.76863 51.6014 5.76867Z" fill="#8A6048"/>
<path d="M53.6419 15.6156C53.6095 15.7365 53.6012 15.8626 53.6175 15.9867C53.6338 16.1108 53.6744 16.2305 53.737 16.339C53.7996 16.4474 53.8829 16.5424 53.9822 16.6186C54.0815 16.6948 54.1949 16.7508 54.3158 16.7832L57.9987 17.7696C58.0791 17.7918 58.1621 17.8031 58.2455 17.803C58.4768 17.8031 58.7003 17.7191 58.8742 17.5666C59.0481 17.4142 59.1607 17.2037 59.1909 16.9744C59.2211 16.7451 59.1669 16.5126 59.0384 16.3203C58.9099 16.128 58.7158 15.989 58.4924 15.9292L54.8095 14.9417C54.6886 14.9093 54.5625 14.901 54.4384 14.9173C54.3142 14.9336 54.1945 14.9742 54.0861 15.0368C53.9777 15.0994 53.8827 15.1827 53.8064 15.282C53.7302 15.3813 53.6743 15.4947 53.6419 15.6156Z" fill="#8A6048"/>
</svg>
                            </div>
                            <div class="comment-form__success-title">Ваш отзыв принят</div>
                            <div class="comment-form__success-descr">Ваш отзыв будет отправлен на модерацию и появится на сайте в течение некоторого времени</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

            <?
            if ($arResult["use_captcha"] === true) {
                ?>
                <div id="captcha_del">
                    <img src="/bitrix/tools/captcha.php?captcha_code=<?= $arResult["CaptchaCode"] ?>"
                         class="captcha_img" width="180" height="40" id="captcha" style="display:none;">
                    <script>
                        document.getElementById('captcha_code').value = '<?=$arResult["CaptchaCode"]?>';
                    </script>
                </div>
                <?
            }
        }

        $prevTab = 0;
        function ShowComment(
            $comment,
            $tabCount = 0,
            $tabSize = 2.5,
            $canModerate = false,
            $User = array(),
            $use_captcha = false,
            $bCanUserComment = false,
            $errorComment = false,
            $arParams = array()
        ) {
            $comment["urlToAuthor"] = "";
            $comment["urlToBlog"] = "";

            if ($comment["SHOW_AS_HIDDEN"] == "Y" || $comment["PUBLISH_STATUS"] == BLOG_PUBLISH_STATUS_PUBLISH || $comment["SHOW_SCREENNED"] == "Y" || $comment["ID"] == "preview") {
                global $prevTab;
                $tabCount = IntVal($tabCount);
                $startVal = $comment['PARENT_ID'] ? 32 : 30;
                if ($tabCount <= 5) {
                    $paddingSize = 26 * $tabCount;
                } elseif ($tabCount > 5 && $tabCount <= 10) {
                    $paddingSize = 26 * 5 + ($tabCount - 5) * 1.5;
                } elseif ($tabCount > 10) {
                    $paddingSize = 26 * 5 + 1.5 * 5 + ($tabCount - 10) * 1;
                }

                if (($tabCount + 1) <= 5) {
                    $paddingSizeNew = 26 * ($tabCount + 1);
                } elseif (($tabCount + 1) > 5 && ($tabCount + 1) <= 10) {
                    $paddingSizeNew = 26 * 5 + (($tabCount + 1) - 5) * 1.5;
                } elseif (($tabCount + 1) > 10) {
                    $paddingSizeNew = 26 * 5 + 1.5 * 5 + (($tabCount + 1) - 10) * 1;
                }
                $paddingSizeNew -= $paddingSize;

                if ($prevTab > $tabCount) {
                    $prevTab = $tabCount;
                }
                if ($prevTab <= 5) {
                    $prevPaddingSize = 26 * $prevTab;
                } elseif ($prevTab > 5 && $prevTab <= 10) {
                    $prevPaddingSize = 26 * 5 + ($prevTab - 5) * 1.5;
                } elseif ($prevTab > 10) {
                    $prevPaddingSize = 26 * 5 + 1.5 * 5 + ($prevTab - 10) * 1;
                }

                $prevTab = $tabCount;
                ?>
                <a name="<?= $comment["ID"] ?>"></a>
            <div class="comment <?= $tabCount > 0 || $comment['PARENT_ID'] ? 'child child-' . $tabCount : 'parent'?>">
                <div id="blg-comment-<?= $comment["ID"] ?>">
                <?

                if (isset($_SESSION['NOT_ADDED_FILES']) && $_SESSION['NOT_ADDED_FILES']['FILES'] && $_SESSION['NOT_ADDED_FILES']['ID'] == $comment["ID"]) { ?>
                    <div class="alert alert-danger"><?
                        print_r(GetMessage('NOT_ADDED_FILES') . '<br />');
                        foreach ($_SESSION['NOT_ADDED_FILES']['FILES'] as $fileName) {
                            echo $fileName . '<br />';
                        }
                        unset($_SESSION['NOT_ADDED_FILES']);
                        ?></div>
                <?
                }


            if ($comment["PUBLISH_STATUS"] == BLOG_PUBLISH_STATUS_PUBLISH || $comment["SHOW_SCREENNED"] == "Y" || $comment["ID"] == "preview") { ?>
                <div class="comment__header">
                    <div class="comment__header-inner">
                        <div class="comment__title">
                            <? if ($tabCount > 0) { ?>
                                <i class="svg inline  svg-inline-arrow_answer" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9">
                                        <defs>
                                            <style>.cls-1{fill:#333;fill-rule:evenodd;}</style>
                                        </defs>
                                        <path
                                            data-name="Rounded Rectangle 1065 copy"
                                            class="cls-1"
                                            d="M332,6417h-6a3,3,0,0,1-3-3v-2.6l-0.312.31a0.973,0.973,0,0,1-1.4,0,1.012,1.012,0,0,1,0-1.42l1.939-1.92c0.022-.02.035-0.06,0.061-0.08a1.007,1.007,0,0,1,1.434,0,0.426,0.426,0,0,1,.052.08l1.948,1.92a1.012,1.012,0,0,1,0,1.42,0.974,0.974,0,0,1-1.406,0L325,6411.4v2.6a1,1,0,0,0,1,1h6A1,1,0,0,1,332,6417Z"
                                            transform="translate(-321 -6408)">
                                        </path>
                                    </svg>
                                </i>
                            <? } ?>
                            <?= $comment["AuthorName"] ?>
                        </div>
                        <div class="comment__date">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="18" viewBox="0 0 16 18" fill="none">
                                <path d="M15 7.50049H1M11.1111 1.50049V4.50049M4.88889 1.50049V4.50049M5.66667 12.0005L7.22222 13.5005L10.7222 10.1255M4.73333 16.5005H11.2667C12.5735 16.5005 13.2269 16.5005 13.726 16.2553C14.165 16.0395 14.522 15.6953 14.7457 15.272C15 14.7907 15 14.1606 15 12.9005V6.60049C15 5.34037 15 4.71031 14.7457 4.22901C14.522 3.80564 14.165 3.46144 13.726 3.24572C13.2269 3.00049 12.5735 3.00049 11.2667 3.00049H4.73333C3.42654 3.00049 2.77315 3.00049 2.27402 3.24572C1.83498 3.46144 1.47802 3.80564 1.25432 4.22901C1 4.71031 1 5.34037 1 6.60049V12.9005C1 14.1606 1 14.7907 1.25432 15.272C1.47802 15.6953 1.83498 16.0395 2.27402 16.2553C2.77315 16.5005 3.42654 16.5005 4.73333 16.5005Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span><?= explode(' ', $comment["DateFormated"])[0] ?></span>
                        </div>
                        <div class="comment__time"><?= explode(' ', $comment["DateFormated"])[1] ?></div>
                    </div>
                    <?
                    if ($comment['AUTHOR_ID'] > 0 && $comment['PARENT_ID'] == false) {
                        global $USER;
                        if ($USER->IsAdmin() && \Redcoon\Statistic\getBuyerStat::checkComments(
                                $comment['AUTHOR_ID']
                            ) && \Redcoon\Statistic\getBuyerStat::checkGivedBonus(
                                $comment['AUTHOR_ID'],
                                $comment['POST_ID'],
                                true
                            )) {
                            ?>
                            <div style="cursor: pointer" data-post="<?= $comment['POST_ID'] ?>"
                                 data-id="<?= $comment['AUTHOR_ID'] ?>" class="giveButton"><span>Начислить баллы</span>
                            </div>
                            <?
                        } ?>
                        <?
                    } ?>
                    <div class="comment__rating">
                        <div class="comment__stars">
                            <? for ($i = 1; $i <= 5; $i++):?>
                                <div class="star <?= $i <= $comment['UF_ASPRO_COM_RATING'] ? 'filled' : '' ?>" style="width:18px; height: 18px"></div>
                            <?endfor; ?>
                        </div>
                    </div>
                </div>

                <div class="comment__main" x-data="{isOpenedComment: false}">
                    <? if (isset($comment["TEXT"]['TYPE']) && $comment["TEXT"]['TYPE'] == 'PARENT'):?>
                        <? if ($comment["TEXT"]['VIRTUES']):?>
                            <div class="comment__main-item">
                                <span class="comment__main-item__title">
                                    <?= GetMessage('BPC_VIRTUES') ?>:
                                </span>
                                <span class="comment__main-item__text">
                                    <?= $comment["TEXT"]['VIRTUES'] ?>
                                </span>
                            </div>
                        <?endif; ?>
                        <? if ($comment["TEXT"]['LIMITATIONS']):?>
                            <div class="comment__main-item">
                                <span class="comment__main-item__title">
                                    <?= GetMessage('BPC_LIMITATIONS') ?>:
                                </span>
                                <span class="comment__main-item__text">
                                    <?= $comment["TEXT"]['LIMITATIONS'] ?>
                                </span>
                            </div>
                        <?endif; ?>
                        <? if ($comment["TEXT"]['COMMENT'] || (!isset($comment["TEXT"]['COMMENT']) && !isset($comment["TEXT"]['LIMITATIONS']))):?>
                            <div class="comment__main-item">
                                <? if ($tabCount == 0) { //only for parents ?>
                                    <span class="comment__main-item__title">
                                        <?= GetMessage('BPC_MESSAGE') ?>:
                                    </span>
                                <? } ?>
                                <span class="comment__main-item__text">
                                    <?= $comment["TEXT"]['COMMENT'] ?? $comment['TextFormated'] ?>
                                </span>
                            </div>
                        <?endif; ?>
                    <? else:?>
                        <? if ($comment["~POST_TEXT"]):?>
                            <?
                            $pattern = '/<comment>(.*?)<\/comment>/s';
                            preg_match($pattern, $comment["~POST_TEXT"], $matches);
                            $commentText = $matches[1];
                            ?>
                            <div class="comment-text__text COMMENT">
                                <?= $commentText ?>
                            </div>
                        <?endif; ?>
                    <?endif; ?>

                    <? if ($comment['IMAGES']):?>
                        <div class="comment-image__wrapper">
                            <? foreach ($comment['IMAGES'] as $arImg):?>
                                <? if ($arImg['FILE_ID']):?>
                                    <? $smalImage = CFile::ResizeImageGet(
                                        $arImg['FILE_ID'],
                                        array("width" => 120, "height" => 120),
                                        BX_RESIZE_IMAGE_PROPORTIONAL_ALT
                                    )['src']; ?>
                                    <a class="comment-image fancy"
                                    data-fancybox="<?= 'comment-' . $comment['ID'] . 'gallery' ?>"
                                    href="<?= CFile::GetPath($arImg['FILE_ID']) ?>"
                                    style="background-image: url(<?= $smalImage ?>);background-size: cover;"></a>
                                <?endif; ?>
                            <?endforeach; ?>
                        </div>
                    <?endif; ?>
                </div>
                <div class="blog-comment-meta">
                    <?
                    global $USER;
                    if ($USER->isAdmin()) {
                        ?>
                        <span class="blog-comment-answer">
                            <a href="javascript:void(0)" class="muted" data-toggle="modal" data-target="#modal-comment" data-parentId="<?= $comment["ID"] ?>">
                                <?= GetMessage("B_B_MS_REPLY") ?>
                            </a>
                        </span>
                        <?
                    }

                    ?>
                </div>

                <div class="blog-clear-float"></div>

                <?
                if (strlen($errorComment) <= 0 && (strlen(
                            $_POST["preview"]
                        ) > 0 && $_POST["show_preview"] != "N") && (IntVal($_POST["parentId"]) > 0 || IntVal(
                            $_POST["edit_id"]
                        ) > 0)
                    && ((IntVal($_POST["parentId"]) == $comment["ID"] && IntVal($_POST["edit_id"]) <= 0)
                        || (IntVal($_POST["edit_id"]) > 0 && IntVal(
                                $_POST["edit_id"]
                            ) == $comment["ID"] && $comment["CAN_EDIT"] == "Y"))) {
                    $level = 0;
                    $commentPreview = array(
                        "ID" => "preview",
                        "TitleFormated" => htmlspecialcharsEx($_POST["subject"]),
                        "TextFormated" => $_POST["commentFormated"],
                        "AuthorName" => $User["NAME"],
                        "DATE_CREATE" => GetMessage("B_B_MS_PREVIEW_TITLE"),
                    );
                    ShowComment(
                        $commentPreview,
                        (IntVal(
                                $_POST["edit_id"]
                            ) == $comment["ID"] && $comment["CAN_EDIT"] == "Y") ? $level : ($level + 1),
                        2.5,
                        false,
                        array(),
                        false,
                        false,
                        false,
                        $arParams
                    );
                }

                if (strlen($errorComment) > 0 && $bCanUserComment === true
                    && (IntVal($_POST["parentId"]) == $comment["ID"] || IntVal($_POST["edit_id"]) == $comment["ID"])) {
                    ?>
                    <div class="alert alert-dangerblog-note-box blog-note-error">
                        <div class="blog-error-text">
                            <?= $errorComment ?>
                        </div>
                    </div>
                    <?
                }
                ?>
                </div>


                <div id="err_comment_<?= $comment['ID'] ?>"></div>
                <div id="form_comment_<?= $comment['ID'] ?>"></div>
                <div id="new_comment_cont_<?= $comment['ID'] ?>" style="margin-left: -7px;"></div>
                <div id="new_comment_<?= $comment['ID'] ?>" style="display:none;"></div>
                <?
                if ((strlen($errorComment) > 0 || strlen($_POST["preview"]) > 0)
                    && (IntVal($_POST["parentId"]) == $comment["ID"] || IntVal($_POST["edit_id"]) == $comment["ID"])
                    && $bCanUserComment === true) {
                    ?>
                    <script>
                        top.text<?=$comment["ID"]?> = text<?=$comment["ID"]?> = '<?=CUtil::JSEscape(
                            $_POST["comment"]
                        )?>';
                        top.title<?=$comment["ID"]?> = title<?=$comment["ID"]?> = '<?=CUtil::JSEscape(
                            $_POST["subject"]
                        )?>';
                        <?
                        if(IntVal($_POST["edit_id"]) == $comment["ID"])
                        {
                        ?>editComment('<?=$comment["ID"]?>');
                        <?
                        }
                        else
                        {
                        ?>showComment('<?=$comment["ID"]?>', 'Y', '<?=CUtil::JSEscape(
                            $_POST["user_name"]
                        )?>', '<?=CUtil::JSEscape($_POST["user_email"])?>', 'Y');<?
                        }
                        ?>
                    </script>
                    <?
                }
            }
            elseif ($comment["SHOW_AS_HIDDEN"] == "Y") {
                echo "<b>" . GetMessage("BPC_HIDDEN_COMMENT") . "</b>";
            }
                ?>
                <? if ($tabCount > 0):?>
                    </div>
                <?endif; ?>
                <?
            }
        }

        function RecursiveComments($sArray,
            $key,
            $level = 0,
            $first = false,
            $canModerate = false,
            $User,
            $use_captcha,
            $bCanUserComment,
            $errorComment,
            $arSumComments,
            $arParams)
        {
        if (!empty($sArray[$key]))
        {
        foreach ($sArray[$key] as $comment)
        {
        if (!empty($arSumComments[$comment["ID"]])) {
            $comment["CAN_EDIT"] = $arSumComments[$comment["ID"]]["CAN_EDIT"];
            $comment["SHOW_AS_HIDDEN"] = $arSumComments[$comment["ID"]]["SHOW_AS_HIDDEN"];
            $comment["SHOW_SCREENNED"] = $arSumComments[$comment["ID"]]["SHOW_SCREENNED"];
            $comment["NEW"] = $arSumComments[$comment["ID"]]["NEW"];
        }
        ShowComment(
            $comment,
            $level,
            2.5,
            $canModerate,
            $User,
            $use_captcha,
            $bCanUserComment,
            $errorComment,
            $arParams
        );
        if (!empty($sArray[$comment["ID"]])) {
            foreach ($sArray[$comment["ID"]] as $key1) {
                if (!empty($arSumComments[$key1["ID"]])) {
                    $key1["CAN_EDIT"] = $arSumComments[$key1["ID"]]["CAN_EDIT"];
                    $key1["SHOW_AS_HIDDEN"] = $arSumComments[$key1["ID"]]["SHOW_AS_HIDDEN"];
                    $key1["SHOW_SCREENNED"] = $arSumComments[$key1["ID"]]["SHOW_SCREENNED"];
                    $key1["NEW"] = $arSumComments[$key1["ID"]]["NEW"];
                }
                ShowComment(
                    $key1,
                    ($level + 1),
                    2.5,
                    $canModerate,
                    $User,
                    $use_captcha,
                    $bCanUserComment,
                    $errorComment,
                    $arParams
                );

                if (!empty($sArray[$key1["ID"]])) {
                    RecursiveComments(
                        $sArray,
                        $key1["ID"],
                        ($level + 2),
                        false,
                        $canModerate,
                        $User,
                        $use_captcha,
                        $bCanUserComment,
                        $errorComment,
                        $arSumComments,
                        $arParams
                    );
                }
            }
        }
        if ($first) {
            $level = 0;
        }

        if ($level == 0): ?>
    </div>
<?endif;
} ?>
    <?
}
}
    ?>
    <?
    if ($arResult["is_ajax_post"] != "Y") {
        if ($arResult["CanUserComment"]) {
            $postTitle = "";
            if ($arParams["NOT_USE_COMMENT_TITLE"] != "Y") {
                $postTitle = "RE: " . CUtil::JSEscape($arResult["Post"]["TITLE"]);
            }
            ?>
            <div class="blog-add-comment"><a class="btn btn-lg btn-transparent-border-color white"
                                             href="javascript:void(0)"><?= GetMessage("B_B_MS_ADD_COMMENT") ?></a></div>
            <a name="0"></a>
            <?
            if (strlen($arResult["COMMENT_ERROR"]) > 0 && strlen($_POST["parentId"]) < 2
                && IntVal($_POST["parentId"]) == 0 && IntVal($_POST["edit_id"]) <= 0) {
                ?>
                <div class="alert alert-danger blog-note-box blog-note-error">
                    <div class="blog-error-text"><?= $arResult["COMMENT_ERROR"] ?></div>
                </div>
                <?
            }
        }

        if ($arResult["CanUserComment"]) {
            ?>

            <div id="form_comment_0">
                <div id="err_comment_0"></div>
                <div id="form_comment_0"></div>
                <div id="new_comment_0" style="display:none;"></div>
            </div>

            <? //include_once('sort.php'); ?>


            <div id="new_comment_cont_0"></div>

            <?
            if ((strlen($arResult["COMMENT_ERROR"]) > 0 || strlen($_POST["preview"]) > 0)
                && IntVal($_POST["parentId"]) == 0 && strlen($_POST["parentId"]) < 2 && IntVal(
                    $_POST["edit_id"]
                ) <= 0) {
                ?>
                <script>
                    top.text0 = text0 = '<?=CUtil::JSEscape($_POST["comment"])?>';
                    top.title0 = title0 = '<?=CUtil::JSEscape($_POST["subject"])?>';
                    showComment('0', 'Y', '<?=CUtil::JSEscape($_POST["user_name"])?>', '<?=CUtil::JSEscape(
                        $_POST["user_email"]
                    )?>', 'Y');
                </script>
                <?
            }
        }
    }

    $arParams["RATING"] = $arResult["RATING"];
    $arParams["component"] = $component;
    $arParams["arImages"] = $arResult["arImages"];
    if ($arResult["is_ajax_post"] == "Y") {
        $arParams["is_ajax_post"] = "Y";
    }

    if ($arResult["is_ajax_post"] != "Y" && $arResult["NEED_NAV"] == "Y") {
        for ($i = 1; $i <= $arResult["PAGE_COUNT"]; $i++) {
            $tmp = $arResult["CommentsResult"];
            $tmp[0] = $arResult["PagesComment"][$i];
            ?>
            <div id="blog-comment-page-<?= $i ?>"<? if ($arResult["PAGE"] != $i) echo "style=\"display:none;\"" ?>><? RecursiveComments(
                    $tmp,
                    $arResult["firstLevel"],
                    0,
                    true,
                    $arResult["canModerate"],
                    $arResult["User"],
                    $arResult["use_captcha"],
                    $arResult["CanUserComment"],
                    $arResult["COMMENT_ERROR"],
                    $arResult["Comments"],
                    $arParams
                ); ?></div>
            <?
        }
    } else {
        if (!$arResult["CommentsResult"][0] && !$arResult["ajax_comment"] && !strlen($arResult["COMMENT_ERROR"])):?>
            <div class="rounded3 alert-empty">
                <?= GetMessage('EMPTY_REVIEWS') ?>
            </div>
            <script>
                var comments = $('.EXTENDED .blog-comments');
                if (comments.length) {
                    comments.addClass('empty-reviews');
                }
            </script>
        <?endif;
        RecursiveComments(
            $arResult["CommentsResult"],
            $arResult["firstLevel"],
            0,
            true,
            $arResult["canModerate"],
            $arResult["User"],
            $arResult["use_captcha"],
            $arResult["CanUserComment"],
            $arResult["COMMENT_ERROR"],
            $arResult["Comments"],
            $arParams
        );
    }

    if ($arResult["is_ajax_post"] != "Y") {
        if ($arResult["NEED_NAV"] == "Y") {
            ?>
            <div class="blog-comment-nav">
                <?
                for ($i = 1; $i <= $arResult["PAGE_COUNT"]; $i++) {
                    $style = "blog-comment-nav-item";
                    if ($i == $arResult["PAGE"]) {
                        $style .= " blog-comment-nav-item-sel colored_theme_bg";
                    }
                    ?><a class="<?= $style ?>" href="#"
                         id="blog-comment-nav-b<?= $i ?>" data-page="<?= $i ?>"><?= $i ?></a><?
                }
                ?>
            </div>
            <?
        }
    }
}
}
?>
    </div>
<?
if ($arResult["is_ajax_post"] == "Y") {
    die();
}

function makeInputsFromParams($arParams, $name = "PARAMS")
{
    $result = "";

    if (is_array($arParams)) {
        foreach ($arParams as $key => $value) {
            if (substr($key, 0, 1) != "~") {
                $inputName = $name . '[' . $key . ']';

                if (is_array($value)) {
                    $result .= makeInputsFromParams($value, $inputName);
                } else {
                    $result .= '<input type="hidden" name="' . $inputName . '" value="' . $value . '">' . PHP_EOL;
                }
            }
        }
    }

    return $result;
}

?>