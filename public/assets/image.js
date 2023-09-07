
var adminImage = {
    copy: function (elem) {

        let copyElem = elem.parents(".image-box").clone(),
            countElem = $(".image-box").length;

        copyElem.find(".preview-box").attr("id", 'input-file' + countElem + '-preview-box');
        copyElem.find("[type='file']").attr({"id": 'input-file' + countElem});
        copyElem.find(".preview-content").remove();
  
        $(".image-box:last").after(copyElem);
    },

    delete: function (elem) {
        elem.parents(".image-box").remove();
        
    },

    remove: function (id) {

        $.ajax({
            url: "/admin/deleteShopItemImage/" + id,
            type: "GET",
            dataType: "json",
            success: function (data) {
                if (data == true) {
                    $("#admin_image_" + id).remove();
                }
            }
        });

    }

}