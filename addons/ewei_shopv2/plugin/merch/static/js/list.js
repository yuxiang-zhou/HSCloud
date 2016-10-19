define(['core', 'tpl'], function (core, tpl) {
    var modal = {page: 1, keyword: '',cateid:''};
    modal.init = function (params) {
        modal.keyword = params.keyword ? params.keyword : '' ;
        modal.cateid = params.cateid ? params.cateid : '' ;
        modal.page = 1;
        $('.fui-content').infinite({
            onLoading: function () {
                modal.getList()
            }
        });
        if (modal.page == 1) {
            modal.getList()
        }
    };
    modal.getList = function () {
        core.json('merch/list/ajaxmerchuser', {page: modal.page, keyword: modal.keyword, cateid: modal.cateid}, function (ret) {
            var result = ret.result;
            if (result.total <= 0) {
                $('.content-empty').show();
                $('.fui-content').infinite('stop')
            } else {
                $('.content-empty').hide();
                $('.container').show();
                $('.fui-content').infinite('init');
                if (result.list.length <= 0 || result.list.length < result.pagesize) {
                    $('.fui-content').infinite('stop')
                }
            }
            modal.page++;
            core.tpl('.container', 'tpl_merch_list_user', result, modal.page > 2);
        })
    };
    return modal
});