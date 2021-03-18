//   Copyright 2019 NEC Corporation
//
//   Licensed under the Apache License, Version 2.0 (the "License");
//   you may not use this file except in compliance with the License.
//   You may obtain a copy of the License at
//
//       http://www.apache.org/licenses/LICENSE-2.0
//
//   Unless required by applicable law or agreed to in writing, software
//   distributed under the License is distributed on an "AS IS" BASIS,
//   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//   See the License for the specific language governing permissions and
//   limitations under the License.
//
//////// ----コールバックファンクション ////////
function callback() {}
callback.prototype = {  
    getOrganizationData : function(result){
        // セッションチェック
        if ( typeof result == "string" ) {
            checkTypicalFlagInHADACResult(getArrayBySafeSeparator(result));
        }

        var organizationAreaWrap = 'Mix1_Nakami';
        var objTableArea = $('#'+organizationAreaWrap+' .table_area').get()[0];

        if(result['result'] == true){
            objTableArea.innerHTML = result['htmlBody'];
            itaTable( 'Mix1_1' );
        }else{
            objTableArea.innerHTML = result['htmlBody'];
        }

        //ボタンの非活性を解除
        $('#Mix1_Nakami').find('input').prop('disabled', false);
    },
    getWorkspaceData : function(result){
        // セッションチェック
        if ( typeof result == "string" ) {
            checkTypicalFlagInHADACResult(getArrayBySafeSeparator(result));
        }

        var workspaceAreaWrap = 'Mix2_Nakami';
        var objTableArea = $('#'+workspaceAreaWrap+' .table_area').get()[0];

        if(result['result'] == true){
            objTableArea.innerHTML = result['htmlBody'];
            itaTable( 'Mix2_1' );
        }else{
            objTableArea.innerHTML = result['htmlBody'];
        }

        //ボタンの非活性を解除
        $('#Mix2_Nakami').find('input').prop('disabled', false);
    },
    getPolicyData : function(result){
        // セッションチェック
        if ( typeof result == "string" ) {
            checkTypicalFlagInHADACResult(getArrayBySafeSeparator(result));
        }

        var policyAreaWrap = 'Mix3_Nakami';
        var objTableArea = $('#'+policyAreaWrap+' .table_area').get()[0];

        if(result['result'] == true){
            objTableArea.innerHTML = result['htmlBody'];
            itaTable( 'Mix3_1' );
        }else{
            objTableArea.innerHTML = result['htmlBody'];
        }

        //ボタンの非活性を解除
        $('#Mix3_Nakami').find('input').prop('disabled', false);
    },
    getPolicySetData : function(result){
        // セッションチェック
        if ( typeof result == "string" ) {
            checkTypicalFlagInHADACResult(getArrayBySafeSeparator(result));
        }

        var policySetAreaWrap = 'Mix4_Nakami';
        var objTableArea = $('#'+policySetAreaWrap+' .table_area').get()[0];

        if(result['result'] == true){
            objTableArea.innerHTML = result['htmlBody'];
            itaTable( 'Mix4_1' );
            //Table Settingを無効（削除）
            $('#Mix4_1_itaTableFooter').remove();
        }else{
            objTableArea.innerHTML = result['htmlBody'];
        }

        //ボタンの非活性を解除
        $('#Mix4_Nakami').find('input').prop('disabled', false);
    },
    deleteOrganization : function(result){
        // セッションチェック
        if ( typeof result == "string" ) {
            checkTypicalFlagInHADACResult(getArrayBySafeSeparator(result));
        }

        if(result['result'] == true){
            //Terraform Enterpriseから{}を削除しました。
            if(window.confirm(getSomeMessage("ITATERRAFORM100021",{0:result['target']}))){
                getOrganizationData();
            }
        }else{
            //Terraform Enterpriseから{}を削除できませんでした。
            if(window.confirm(getSomeMessage("ITATERRAFORM100022",{0:result['target']}))){
                getOrganizationData();
            }
        }
    },
    deleteWorkspace : function(result){
        // セッションチェック
        if ( typeof result == "string" ) {
            checkTypicalFlagInHADACResult(getArrayBySafeSeparator(result));
        }

        if(result['result'] == true){
            //Terraform Enterpriseから{}を削除しました。
            if(window.confirm(getSomeMessage("ITATERRAFORM100021",{0:result['target']}))){
                getWorkspaceData();
            }
        }else{
            //Terraform Enterpriseから{}を削除できませんでした。
            if(window.confirm(getSomeMessage("ITATERRAFORM100022",{0:result['target']}))){
                getWorkspaceData();
            }
        }
    },
    deletePolicy : function(result){
        // セッションチェック
        if ( typeof result == "string" ) {
            checkTypicalFlagInHADACResult(getArrayBySafeSeparator(result));
        }

        if(result['result'] == true){
            //Terraform Enterpriseから{}を削除しました。
            if(window.confirm(getSomeMessage("ITATERRAFORM100021",{0:result['target']}))){
                getPolicyData();
            }
        }else{
            //Terraform Enterpriseから{}を削除できませんでした。
            if(window.confirm(getSomeMessage("ITATERRAFORM100022",{0:result['target']}))){
                getPolicyData();
            }
        }
    },
    deletePolicySet : function(result){
        // セッションチェック
        if ( typeof result == "string" ) {
            checkTypicalFlagInHADACResult(getArrayBySafeSeparator(result));
        }

        if(result['result'] == true){
            //Terraform Enterpriseから{}を削除しました。
            if(window.confirm(getSomeMessage("ITATERRAFORM100021",{0:result['target']}))){
                getPolicySetData();
            }
        }else{
            //Terraform Enterpriseから{}を削除できませんでした。
            if(window.confirm(getSomeMessage("ITATERRAFORM100022",{0:result['target']}))){
                getPolicySetData();
            }
        }
    },
    deleteRelationshipWorkspace : function(result){
        // セッションチェック
        if ( typeof result == "string" ) {
            checkTypicalFlagInHADACResult(getArrayBySafeSeparator(result));
        }

        policySetName = result['policySetName'];
        workspaceName = result['workspaceName'];
        if(result['result'] == true){
            //{}を{}から切り離しました。
            if(window.confirm(getSomeMessage("ITATERRAFORM100023",{0:workspaceName, 1:policySetName}))){
                getPolicySetData();
            }
        }else{
            //{}を{}からの切り離しに失敗しました。
            if(window.confirm(getSomeMessage("ITATERRAFORM100024",{0:workspaceName, 1:policySetName}))){
                getPolicySetData();
            }
        }
    },
    deleteRelationshipPolicy : function(result){
        // セッションチェック
        if ( typeof result == "string" ) {
            checkTypicalFlagInHADACResult(getArrayBySafeSeparator(result));
        }

        policySetName = result['policySetName'];
        policyName = result['policyName'];
        if(result['result'] == true){
            //{}を{}から切り離しました。
            if(window.confirm(getSomeMessage("ITATERRAFORM100023",{0:policyName, 1:policySetName}))){
                getPolicySetData();
            }
        }else{
            //{}を{}からの切り離しに失敗しました。
            if(window.confirm(getSomeMessage("ITATERRAFORM100024",{0:policyName, 1:policySetName}))){
                getPolicySetData();
            }
        }
    }
}

var proxy = new Db_Access(new callback());

window.onload = function(){
    var menu_on = $('.menu-on').attr('id');
    $('.menu_on').val(menu_on);

    show('SetsumeiMidashi', 'SetsumeiNakami');
}



function getOrganizationData(){
    var organizationAreaWrap = 'Mix1_Nakami';
    //ボタンを非活性化
    $('#'+organizationAreaWrap).find('input').attr('disabled', true);

    // しばらくお待ち下さいを出す
    var objTableArea = $('#'+organizationAreaWrap+' .table_area').get()[0];
    objTableArea.innerHTML = "<div class=\"wait_msg2\" >"+getSomeMessage("ITAWDCC10102")+"</div>";

    // TerraformEnterpriseからOrganizationを取得し一覧表示
    proxy.getOrganizationData();
}

function getWorkspaceData(){
    var workspaceAreaWrap = 'Mix2_Nakami';
    //ボタンを非活性化
    $('#'+workspaceAreaWrap).find('input').attr('disabled', true);

    // しばらくお待ち下さいを出す
    var objTableArea = $('#'+workspaceAreaWrap+' .table_area').get()[0];
    objTableArea.innerHTML = "<div class=\"wait_msg2\" >"+getSomeMessage("ITAWDCC10102")+"</div>";

    // TerraformEnterpriseからWorkspaceを取得し一覧表示
    proxy.getWorkspaceData();
}

function getPolicyData(){
    var policyAreaWrap = 'Mix3_Nakami';
    //ボタンを非活性化
    $('#'+policyAreaWrap).find('input').attr('disabled', true);

    // しばらくお待ち下さいを出す
    var objTableArea = $('#'+policyAreaWrap+' .table_area').get()[0];
    objTableArea.innerHTML = "<div class=\"wait_msg2\" >"+getSomeMessage("ITAWDCC10102")+"</div>";

    // TerraformEnterpriseからPolicyを取得し一覧表示
    proxy.getPolicyData();
}

function getPolicySetData(){
    var policySetAreaWrap = 'Mix4_Nakami';
    //ボタンを非活性化
    $('#'+policySetAreaWrap).find('input').attr('disabled', true);

    // しばらくお待ち下さいを出す
    var objTableArea = $('#'+policySetAreaWrap+' .table_area').get()[0];
    objTableArea.innerHTML = "<div class=\"wait_msg2\" >"+getSomeMessage("ITAWDCC10102")+"</div>";

    // TerraformEnterpriseからPolicySetを取得し一覧表示
    proxy.getPolicySetData();
}

function deleteOrganization(obj, organizationName){
    //エンコード
    organizationName = encodeURIComponent(organizationName);
    var data = {
        'organizationName' : organizationName
    };

    //{}をTerraform Enterpriseから削除します。削除されたOrganizationは元に戻せません。
    if( window.confirm(getSomeMessage("ITATERRAFORM100025",{0:organizationName}))){
        proxy.deleteOrganization(data);
    }
}

function deleteWorkspace(obj, organizationName, workspaceName){
    //エンコード
    organizationName = encodeURIComponent(organizationName);
    workspaceName = encodeURIComponent(workspaceName);
    var data = {
        'organizationName' : organizationName,
        'workspaceName' : workspaceName
    };

    //{}をTerraform Enterpriseから削除します。削除されたWorkspaceは元に戻せません。
    if( window.confirm(getSomeMessage("ITATERRAFORM100026",{0:workspaceName}))){
        proxy.deleteWorkspace(data);
    }
}

function deletePolicy(obj, policyId, policyName){
    //エンコード
    policyId = encodeURIComponent(policyId);
    policyName = encodeURIComponent(policyName);
    var data = {
        'policyId' : policyId,
        'policyName' : policyName
    };

    //{}をTerraform Enterpriseから削除します。削除されたPolicyは元に戻せません。
    if( window.confirm(getSomeMessage("ITATERRAFORM100027",{0:policyName}))){
        proxy.deletePolicy(data);
    }
}

function deletePolicySet(obj, policySetId, policySetName){
    //エンコード
    policySetId = encodeURIComponent(policySetId);
    policySetName = encodeURIComponent(policySetName);
    var data = {
        'policySetId' : policySetId,
        'policySetName' : policySetName
    };

    //{}をTerraform Enterpriseから削除します。削除されたPolicySetは元に戻せません。
    if( window.confirm(getSomeMessage("ITATERRAFORM100028",{0:policySetName}))){
        proxy.deletePolicySet(data);
    }
}

function deleteRelationshipWorkspace(obj, policySetId, policySetName, workspaceId, workspaceName){
    //エンコード
    policySetId = encodeURIComponent(policySetId);
    policySetName = encodeURIComponent(policySetName);
    workspaceId = encodeURIComponent(workspaceId);
    workspaceName = encodeURIComponent(workspaceName);
    var data = {
        'policySetId' : policySetId,
        'policySetName' : policySetName,
        'workspaceId' : workspaceId,
        'workspaceName' : workspaceName
    };

    //{}を{}から切り離します。
    if( window.confirm(getSomeMessage("ITATERRAFORM100029",{0:policySetName, 1:workspaceName}))){
        proxy.deleteRelationshipWorkspace(data);
    }
}

function deleteRelationshipPolicy(obj, policySetId, policySetName, policyId, policyName){
    //エンコード
    policySetId = encodeURIComponent(policySetId);
    policySetName = encodeURIComponent(policySetName);
    policyId = encodeURIComponent(policyId);
    policyName = encodeURIComponent(policyName);
    var data = {
        'policySetId' : policySetId,
        'policySetName' : policySetName,
        'policyId' : policyId,
        'policyName' : policyName
    };

    //{}を{}から切り離します。
    if( window.confirm(getSomeMessage("ITATERRAFORM100029",{0:policySetName, 1:policyName}))){
        proxy.deleteRelationshipPolicy(data);
    }
}