        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
            <legend>扩展字段</legend>
        </fieldset>
<?php
//读取扩展字段列表
$list = get_db("user_config","v",["k"=>"s_extend_list","uid"=>UID]);
//不为空则渲染
if(!empty($list)){
    $list = unserialize($list);
    $extend_data = get_db('user_links','extend',['uid'=>UID,'lid'=>$link['lid']]);
    $extend_data = empty($extend_data) ? [] : unserialize($extend_data);

    foreach ($list as $data) {
        $field = "_".$data['name'];
        $data['value'] = isset($extend_data[$field]) ?  $extend_data[$field] : $data['default'];
        $link["_{$data['name']}"] = ''.htmlentities($data['value']);
        if($data['type'] == 'text'){
            echo_text($data);
        }elseif($data['type'] == 'textarea'){
            echo_textarea($data);
        }elseif($data['type'] == 'up_img'){
            echo_up_img($data);
        }
    }
}
   
function echo_text($data){ ?>
        <div class="layui-form-item">
            <label class="layui-form-label" title="<?php echo $data['name']?>"><?php echo $data['title']?></label>
            <div class="layui-input-block">
                <input type="text" name="_<?php echo $data['name']?>" autocomplete="off" value="<?php echo htmlentities($data['value'])?>" placeholder="<?php echo htmlentities($data['tip'])?>" class="layui-input" >
            </div>
        </div>
<?php 
}

function echo_textarea($data){ ?>
        <div class="layui-form-item">
            <label class="layui-form-label" title="<?php echo $data['name']?>"><?php echo $data['title']?></label>
            <div class="layui-input-block">
                <textarea name="_<?php echo $data['name']?>" placeholder="<?php echo htmlentities($data['tip'])?>" class="layui-textarea"><?php echo htmlentities($data['value'])?></textarea>
            </div>
        </div>
<?php 
}  

function echo_up_img($data){ ?>
        <div class="layui-form-item">
            <label class="layui-form-label" title="<?php echo $data['name']?>"><?php echo $data['title']?></label>
            <div class="layui-input-block layuimini-upload" >
                <input type="text" name="_<?php echo $data['name']?>" autocomplete="off" class="layui-input obtn" value="<?php echo htmlentities($data['value'])?>" placeholder="<?php echo htmlentities($data['tip'])?>">
                <div style="position: absolute; top:0px;" >
                    <span><a class="layui-btn layui-btn-primary extend_up_img"><i class="fa fa-upload"></i> 上传</a></span>
                </div>
            </div>
        </div>
<?php 
} 


?>


