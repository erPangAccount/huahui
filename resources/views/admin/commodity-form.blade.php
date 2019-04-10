<div id="has-many-skus-attribute" class="has-many-skus-attribute">
    <div class="has-many-skus-attribute-forms"></div>

    <template class="skus-attribute-tpl">
        <div class="has-many-skus-form fields-group" id="skus_attribute[__INDEX__][new___LA_KEY__]">
            <div class="form-group  ">
                <div class="col-sm-6">
                    <div class="col-sm-12">
                        <label for="skus_attribute[__INDEX__][new___LA_KEY__][attribute_id]"
                               class="col-sm-4  control-label">属性名</label>
                        <div class="col-sm-8">
                            <select class="form-control skus_attribute[__INDEX__][new___LA_KEY__][attribute_id]"
                                    style="width: 100%;" name="skus_attribute[__INDEX__][new___LA_KEY__][attribute_id]"
                                    data-value="">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <label class="col-sm-4  control-label">属性值</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                            <input type="text" name="skus_attribute[__INDEX__][new___LA_KEY__][skus_attribute_value]" value=""
                                   class="form-control skus skus_name" placeholder="输入 属性值">
                        </div>
                    </div>
                </div>

            </div>
            <hr style="margin-top: 0px;">

            <input type="hidden" name="skus_attribute[__INDEX__][new___LA_KEY__][_remove_]" value="0"
                   class="skus_attribute _remove_ fom-removed">

            <div class="form-group">
                <label class="col-sm-2  control-label"></label>
                <div class="col-sm-8">
                    <div class="btn btn-warning btn-sm pull-right" onclick="removeSkuAttribute(this.parentElement.parentElement.parentElement)"><i
                                class="fa fa-trash"></i>&nbsp;移除
                    </div>
                </div>
            </div>
            <hr>
        </div>
    </template>

    <div class="form-group">
        <label class="col-sm-2  control-label"></label>
        <div class="col-sm-8">
            <div class="btn btn-success btn-sm" onclick="addSkuAttribute(this.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement)"><i class="fa fa-save"></i>&nbsp;新增</div>
        </div>
    </div>
</div>

<script>
    function addSkuAttribute(element) {
        var index;
        for (var i = 0; i<element.children.length;i++) {
            if (element.children[i].tagName === 'INPUT' && element.children[i].className === 'skus _remove_ fom-removed') {
                index = parseInt(element.children[i].name.split('_')[1].replace("/[^0-9]/ig", ''));
            }
        }

        var attributeParentHtmlElement = document.getElementsByClassName('has-many-skus-attribute-forms')[index - 1];
        var attributeRealAppendChildArr = attributeParentHtmlElement.children;
        var attributeTemplateHtmlElement;
        for (i = 0; i < attributeParentHtmlElement.parentElement.children.length; i++) {
            if (attributeParentHtmlElement.parentElement.children[i].tagName === 'TEMPLATE') {
                attributeTemplateHtmlElement = attributeParentHtmlElement.parentElement.children[i];
                break;
            }
        }

        var nowAppendHtml = attributeTemplateHtmlElement.innerHTML.replace(RegExp("new___LA_KEY__", "g"), attributeRealAppendChildArr.length ? parseInt(attributeRealAppendChildArr[attributeRealAppendChildArr.length - 1].getAttribute('id').split('[')[2].replace("/[^0-9]/ig", '')) + 1 : 0);
        nowAppendHtml = nowAppendHtml.replace(RegExp("__INDEX__", "g"), index);
        attributeParentHtmlElement.innerHTML += nowAppendHtml;
    }

    function removeSkuAttribute(element) {
        var child=document.getElementById(element.getAttribute('id'));
        child.parentNode.removeChild(child);
    }

</script>