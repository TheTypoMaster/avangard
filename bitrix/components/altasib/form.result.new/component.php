<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?

if (CModule::IncludeModule("form"))
{
        if (!isset($arParams["CACHE_TIME"])) $arParams["CACHE_TIME"] = "3600";

        // create form output class
        $arParams["COMPONENT"] = array(
                "componentName" =>                 $componentName,
                "componentTemplate" =>         $componentTemplate,
                "componentPath" =>                 $componentPath,
        );

        $bCache = !($_SERVER["REQUEST_METHOD"] == "POST" && (!empty($_REQUEST["web_form_submit"]) || !empty($_REQUEST["web_form_apply"]))) && !($arParams["CACHE_TYPE"] == "N" || ($arParams["CACHE_TYPE"] == "A" && COption::GetOptionString("main", "component_cache_on", "Y") == "N"));

        // start caching
        if ($bCache)
        {
                // append arParams to cache ID;
                $arCacheParams = array();
                foreach ($arParams as $key => $value) if (substr($key, 0, 1) != "~") $arCacheParams[$key] = $value;
                // create CPHPCache class instance
                $cache_form = new CPHPCache;
                // create cache ID and path
                $CACHE_ID = SITE_ID."|".$componentName."|".md5(serialize($arCacheParams))."|".$USER->GetGroups();
                $CACHE_PATH = "/".SITE_ID.CComponentEngine::MakeComponentPath($componentName);
        }

        // initialize cache
        if ($bCache && $cache_form->InitCache($arParams["CACHE_TIME"], $CACHE_ID, $CACHE_PATH))
        {
                // if cache already exists - get vars
                $vars = $cache_form->GetVars();
                $FORM = $vars["FORM"];
        }
        else
        {
                // process form
                $FORM = new CFormOutput();
                //$res = $FORM->Init($arParams);

/*************************************************************/
                $FORM->bSimple = (COption::GetOptionString("form", "SIMPLE", "Y") == "Y") ? true : false;
                $FORM->comp2 = true;
                $FORM->SHOW_INCLUDE_AREAS = $APPLICATION->GetShowIncludeAreas();

                $FORM->arParams = $arParams;

                if (intval($FORM->WEB_FORM_ID) <= 0)
                        $FORM->WEB_FORM_ID = intval($arParams["WEB_FORM_ID"]);

                // if there's no WEB_FORM_ID, try to get it from $_REQUEST;
                if (intval($FORM->WEB_FORM_ID) <= 0)
                        $FORM->WEB_FORM_ID = intval($_REQUEST["WEB_FORM_ID"]);

                // check WEB_FORM_ID and get web form data
                $FORM->WEB_FORM_ID = CForm::GetDataByID($FORM->WEB_FORM_ID, $FORM->arForm, $FORM->arQuestions, $FORM->arAnswers, $FORM->arDropDown, $FORM->arMultiSelect, "N", $FORM->arParams["SHOW_ADDITIONAL"] == "Y" || $FORM->arParams["EDIT_ADDITIONAL"] == "Y" ? "Y" : "N");

                $FORM->WEB_FORM_NAME = $FORM->arForm["SID"];

                // if wrong WEB_FORM_ID return error;
                if ($FORM->WEB_FORM_ID > 0)
                {
                        // check web form rights;
                        $FORM->F_RIGHT = intval(CForm::GetPermission($FORM->WEB_FORM_ID));

                        // in no form access - return error
                        if ($FORM->isAccessForm())
                        {
                                if (!empty($_REQUEST["strFormNote"])) $FORM->strFormNote = $_REQUEST["strFormNote"];
                        }
                        else
                        {
                                $FORM->setError("FORM_ACCESS_DENIED");
                        } // endif ($F_RIGHT>=10);
                }
                else
                {
                        $FORM->setError("FORM_NOT_FOUND");
                } // endif ($WEB_FORM_ID>0);
/*************************************************************/

                // additional caching
                if ($bCache && $FORM->isAccessForm() && $cache_form->StartDataCache())
                {
                        // cache form image path and code
                        $FORM->ShowFormImage();
                        $FORM->getFormImagePath();

                        // cache form question images path and code
                        foreach ($FORM->arQuestions as $FIELD_SID => $arQuestion)
                        {
                                $FORM->ShowInputCaptionImage($FIELD_SID);
                                $FORM->getInputCaptionImagePath($FIELD_SID);
                        }

                        // put $FORM to cache
                        $cache_form->EndDataCache(array(
                                "FORM" => $FORM,
                        ));
                }
        }

        $bFormShow = strlen($FORM->ShowErrorMsg()) <= 0;

        // show form
        if ($bFormShow)
        {
                //  insert chain item
                if (strlen($FORM->arParams["CHAIN_ITEM_TEXT"]) > 0)
                {
                        $APPLICATION->AddChainItem($FORM->arParams["CHAIN_ITEM_TEXT"], $FORM->arParams["CHAIN_ITEM_LINK"]);
                }

                // initialize CAPTCHA
                if ($FORM->arForm["USE_CAPTCHA"] == "Y") $FORM->CaptchaInitialize();

                // get additional data from $FORM and process form result;
                $arResult = $FORM->getData($arResult);

                // include CSS with additional icons for Site Edit mode
                if ($APPLICATION->GetShowIncludeAreas() && $USER->IsAdmin())
                {
                        $APPLICATION->SetAdditionalCSS($this->GetPath()."/icons.css");
                        // define additional icons for Site Edit mode
                        $arIcons = array(
                                // form template edit icon
                                array(
                                        'URL' => "/bitrix/admin/form_edit.php?lang=".LANGUAGE_ID."&ID=".$FORM->WEB_FORM_ID."&tabControl_active_tab=edit5&back_url=".urlencode($_SERVER["REQUEST_URI"]),
                                        'ICON' => 'form-edit-tpl',
                                        'TITLE' => GetMessage("FORM_PUBLIC_ICON_EDIT_TPL")
                                ),

                                // form params edit icon
                                array(
                                        'URL' => "/bitrix/admin/form_edit.php?lang=".LANGUAGE_ID."&ID=".$FORM->WEB_FORM_ID."&back_url=".urlencode($_SERVER["REQUEST_URI"]),
                                        'ICON' => 'form-edit',
                                        'TITLE' => GetMessage("FORM_PUBLIC_ICON_EDIT")
                                ),
                        );

                        // append icons
                        $this->AddIncludeAreaIcons($arIcons);
                }

                /*
                $FORM->setInputDefaultValue("SIMPLE_QUESTION_962", "te\"st1");
                $FORM->setInputDefaultValue("SIMPLE_QUESTION_862", "tes\"><script>alert('Test')</script>t2");
                $FORM->setInputDefaultValue("SIMPLE_QUESTION_415", "http://test2");
                $FORM->setInputDefaultValue("SIMPLE_QUESTION_295", "ad'fhgiadfg;iosaHDg;aejkrghaer;liya;SDlkgnyier");
                //$FORM->setInputDefaultValue("SIMPLE_QUESTION_617", 1103);
                $FORM->setInputDefaultValue("SIMPLE_QUESTION_617", "Y", 1103);
                //$FORM->setInputDefaultValue("SIMPLE_QUESTION_923", array(1105, 1106));
                $FORM->setInputDefaultValue("SIMPLE_QUESTION_923", "Y", array(1105, 1106));
                //$FORM->setInputDefaultValue("SIMPLE_QUESTION_329", 1109);
                $FORM->setInputDefaultValue("SIMPLE_QUESTION_329", "Y", 1109);
                $FORM->setInputDefaultValue("SIMPLE_QUESTION_635", ConvertTimeStamp());

                $FORM->setInputDefaultValue("SIMPLE_QUESTION_535", "test");
                $FORM->setInputDefaultValue("SIMPLE_QUESTION_900", "Y", array(1114, 1115));
                */

                // output form
                if ($arParams["IGNORE_CUSTOM_TEMPLATE"] == "Y" || !$FORM->IncludeFormTemplate())
                {
                        // if there's no custom form template - use default one

                        // define variables to assign
                        $arResult = array_merge(
                                $arResult,
                                array(
                                        "FORM_ERRORS" => $FORM->ShowFormErrors(), // form errors
                                        "FORM_NOTE" => $FORM->ShowFormNote(), // form note
                                        "isFormErrors" => $FORM->isFormErrors() ? "Y" : "N", // flag "are there any form errors"
                                        "isFormNote" => $FORM->isFormNote() ? "Y" : "N", // flag "is there a form note"
                                        "isAccessFormParams" => $FORM->isAccessFormParams() ? "Y" : "N", // flag "does current user have access to form params"
                                        "isStatisticIncluded" => $FORM->isStatisticIncluded() ? "Y" : "N", // flag "is statistic module included"
                                        "FORM_HEADER" => $FORM->ShowFormHeader(), // form header (<form> tag and hidden inputs)
                                        "FORM_TITLE" => $FORM->ShowFormTitle(), // form title
                                        "FORM_DESCRIPTION" => $FORM->ShowFormDescription(), // form description
                                        "isFormTitle" => $FORM->isFormTitle() ? "Y" : "N", // flag "does form have title"
                                        "isFormDescription" => $FORM->isFormDescription() ? "Y" : "N", // flag "does form have description"
                                        "isFormImage" => $FORM->isFormImage() ? "Y" : "N", // flag "does form have image"
                                        "isUseCaptcha" => $FORM->isUseCaptcha() ? "Y" : "N", // flag "does form use captcha"
                                        "CAPTCHA_IMAGE" => $FORM->ShowCaptchaImage(), // captcha images
                                        "CAPTCHA_FIELD" => $FORM->ShowCaptchaField(), // captcha code input field
                                        "CAPTCHA" => $FORM->ShowCaptcha(), // both captcha field and image
                                        "REQUIRED_STAR" => $FORM->ShowRequired(), // "required" star
                                        "DATE_FORMAT" => $FORM->ShowDateFormat(), // current site date format
                                        "SUBMIT_BUTTON" => $FORM->ShowSubmitButton(), // form submit button
                                        "APPLY_BUTTON" => $FORM->ShowApplyButton(), // form apply button
                                        "RESET_BUTTON" => $FORM->ShowResetButton(), // form reset button
                                        "FORM_FOOTER" => $FORM->ShowFormFooter(), // form footer (close <form> tag)
                                )
                        );

                        // get template vars for form image
                        if ($FORM->isFormImage())
                        {
                                $arResult["FORM_IMAGE"]["ID"] = $FORM->arForm["IMAGE_ID"];
                                // assign form image url
                                $arResult["FORM_IMAGE"]["URL"] = $FORM->getFormImagePath();

                                // check image file existance and assign image data
                                if (
                                        file_exists($_SERVER["DOCUMENT_ROOT"].$arResult["FORM_IMAGE"]["URL"])
                                        &&
                                        list(
                                                $arResult["FORM_IMAGE"]["WIDTH"],
                                                $arResult["FORM_IMAGE"]["HEIGHT"],
                                                $arResult["FORM_IMAGE"]["TYPE"],
                                                $arResult["FORM_IMAGE"]["ATTR"]
                                        ) = @getimagesize($_SERVER["DOCUMENT_ROOT"].$arResult["FORM_IMAGE"]["URL"])
                                )
                                {
                                        $arResult["FORM_IMAGE"]["HTML_CODE"] = $FORM->ShowFormImage();
                                }
                        }

                        $arResult["QUESTIONS"] = array();
                        reset($FORM->arQuestions);

                        // assign questions data
                        foreach ($FORM->arQuestions as $key => $arQuestion)
                        {
                                $FIELD_SID = $arQuestion["SID"];
                                $arResult["QUESTIONS"][$FIELD_SID] = array(
                                        "HTML_CODE" => $FORM->ShowInput($FIELD_SID), // field HTML code
                                        "CAPTION" => $FORM->ShowInputCaption($FIELD_SID), // field caption
                                        "CAPTION_UNFORM" => $FORM->arQuestions[$FIELD_SID]["TITLE"], // raw field caption
                                        "IS_HTML_CAPTION" => $FORM->arQuestions[$FIELD_SID]["TITLE_TYPE"] == "html" ? "Y" : "N",
                                        "REQUIRED" => $FORM->arQuestions[$FIELD_SID]["REQUIRED"] == "Y" ? "Y" : "N",
                                        "IS_INPUT_CAPTION_IMAGE" => $FORM->isInputCaptionImage($FIELD_SID) ? "Y" : "N",
                                );

                                if ($FORM->isInputCaptionImage($FIELD_SID))
                                {
                                        $arResult["QUESTIONS"][$FIELD_SID]["IMAGE"]["ID"] = $FORM->arQuestions[$FIELD_SID]["IMAGE_ID"];
                                        //$arResult["QUESTIONS"][$FIELD_SID]["IMAGE"]["URL"] = CFile::GetPath($FORM->arQuestions[$FIELD_SID]["IMAGE_ID"]);

                                        // assign field image path
                                        $arResult["QUESTIONS"][$FIELD_SID]["IMAGE"]["URL"] = $FORM->getInputCaptionImagePath($FIELD_SID);

                                        // check image file existance and assign image data
                                        if (
                                                file_exists($_SERVER["DOCUMENT_ROOT"].$arResult["QUESTIONS"][$FIELD_SID]["IMAGE"]["URL"])
                                                &&
                                                list(
                                                        $arResult["QUESTIONS"][$FIELD_SID]["IMAGE"]["WIDTH"],
                                                        $arResult["QUESTIONS"][$FIELD_SID]["IMAGE"]["HEIGHT"],
                                                        $arResult["QUESTIONS"][$FIELD_SID]["IMAGE"]["TYPE"],
                                                        $arResult["QUESTIONS"][$FIELD_SID]["IMAGE"]["ATTR"]
                                                ) = @getimagesize($_SERVER["DOCUMENT_ROOT"].$arResult["QUESTIONS"][$FIELD_SID]["IMAGE"]["URL"])
                                        )
                                        {
                                                $arResult["QUESTIONS"][$FIELD_SID]["IMAGE"]["HTML_CODE"] = $FORM->ShowInputCaptionImage($FIELD_SID);
                                        }
                                }

                                // get answers raw structure
                                $arResult["QUESTIONS"][$FIELD_SID]["STRUCTURE"] = $FORM->arAnswers[$FIELD_SID];

                                // nullify value
                                $arResult["QUESTIONS"][$FIELD_SID]["VALUE"] = "";
                        }

                        // include default template
                        $this->IncludeComponentTemplate();
                }
        }
        else
        {
                echo ShowError(GetMessage($FORM->ShowErrorMsg()));
        }
}
else
{
        echo ShowError(GetMessage("FORM_MODULE_NOT_INSTALLED"));
}
?>
