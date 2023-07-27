<?php 
if($global_config['article'] != 1 || !check_purview('article',1)){
    require(DIR.'/templates/admin/page/404.php');
    exit;
}
$article_id = Get('id');
$mode = empty($article_id) ? 'add' : 'edit' ;

if($mode == 'edit'){
    if(has_db('user_article_list',['uid'=>UID,'id'=>$article_id])){
        $data = get_db('user_article_list','*',['uid'=>UID,'id'=>$article_id]);
        //var_dump($data);
    }else{
        $mode = 'add';
    }
    
}

$title = $mode == 'add' ? '添加文章' : '编辑文章';

function echo_article_category(){
    $where['uid'] = UID; 
    foreach (select_db('user_article_categorys','*',$where) as $category) {
        echo "<option value=\"{$category['id']}\">{$category['name']}</option>";
    }
}

require dirname(__DIR__).'/header.php'  ?>
<link href="<?php echo $libs?>/wangEditor/wangEditor.css" rel="stylesheet">
<style type="text/css">
  #editor—wrapper { border: 1px solid #cccccc88; }
  #toolbar-container { border-bottom: 1px solid #ccc; }
  #editor-container { height: 400px; }
  .w40{width:40px;}
  .layui-input-block{margin-left: 70px;}
  @media screen and (max-width: 768px) {
      .layui-input-block {margin-left: 12px;}
      .content{display: none!important;}
      .layui-form-select .layui-edge {top: 75%;}
  }
</style>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
      <form class="layui-form" lay-filter="form">
        <input class="layui-input layui-hide" name="id" autocomplete="off" value="<?php echo $data['id'];?>">
        <div class="layui-form-item ">
         <label class="layui-form-label w40">标题:</label>
          <div class="layui-input-block">
           <input class="layui-input" name="title" placeholder='请输入文章标题' autocomplete="off" value="<?php echo $data['title'];?>">
          </div>
        </div>
        
        <div class="layui-form-item">
         <label class="layui-form-label w40">分类:</label>
          <div class="layui-input-block">
            <select name="category" lay-search>
                <?php echo_article_category(); ?>
            </select>
          </div>
        </div>
        
        <div class="layui-form-item">
         <label class="layui-form-label w40">状态:</label>
          <div class="layui-input-block">
            <select name="state">
                <option value="1">公开</option>
                <option value="2">私有</option>
                <option value="3">草稿</option>
                <option value="4">废弃</option>
            </select>
          </div>
        </div>
        
        <div class="layui-form-item">
         <label class="layui-form-label w40">摘要:</label>
          <div class="layui-input-block">
            <textarea name="summary" rows ="2" placeholder="文章摘要,留空时自动获取" class="layui-textarea"><?php echo $data['summary'];?></textarea>
          </div>
        </div>
    
        <div class="layui-form-item">
         <label class="layui-form-label w40 content">正文:</label>
          <div class="layui-input-block" id="editor—wrapper">
            <div id="toolbar-container"></div>
            <div id="editor-container"></div>
            <textarea name="content" id="content" class="layui-textarea layui-hide"><?php echo $data['content'] ?? '<p><br></p>';?></textarea>
          </div>
        </div>
        
      </form>
      
      <div class="layui-form-item">
          <div class="layui-input-block">
              <button class="layui-btn layui-btn-normal layui-btn-danger" id="cancel" >取消</button>
              <button class="layui-btn layui-btn-normal" id="save" >保存</button>
          </div>
      </div>
    </div>
</div>
<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src = '<?php echo $libs?>/jquery/jquery.md5.js'></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<?php load_static('js');?>
<script src="<?php echo $libs?>/wangEditor/wangEditor.js"></script>
<script>

const { createEditor, createToolbar } = window.wangEditor
const editorConfig = {
    placeholder: '请输入文章内容...',
    MENU_CONF: {
        uploadImage: {}
    },
    onChange(editor) {
      const html = editor.getHtml();
      $('#content').val(html);
    }
}

editorConfig.MENU_CONF['uploadImage'] = {
    base64LimitSize: 128 * 1024, //小于该值就插入base64
    server: get_api('write_article','uploadImage'),
    fieldName: 'file',
    maxFileSize: 5 * 1024 * 1024, // 单文件限制5M
    maxNumberOfFiles: 10, //最多上传10个文件
    // 上传之前触发
    onBeforeUpload(file) {
        return file
    },
    // 上传进度的回调函数
    onProgress(progress) {
        console.log('progress', progress) //进度: 0-100
    },
    // 单个文件上传成功之后
    onSuccess(file, res) {
        parent.layer.msg('上传成功', {icon: 1});
    },
    // 单个文件上传失败
    onFailed(file, res) {
        layer.alert(`${res.message}`,{icon:5,title:`上传失败: ${file.name}`,anim: 2,closeBtn: 0});
        console.log(res );
    },
    // 上传错误，或者触发 timeout 超时
     onError(file, err, res) {
        layer.alert(`${err}`,{icon:5,title:`上传错误: ${file.name}`,anim: 2,closeBtn: 0});
    },
}
editorConfig.MENU_CONF['uploadVideo'] = {
    base64LimitSize: 128 * 1024, //小于该值就插入base64
    server: get_api('write_article','uploadVideo'),
    fieldName: 'file',
    maxFileSize: 20 * 1024 * 1024, // 单文件限制
    maxNumberOfFiles: 10, //最多上传10个文件
    // 上传之前触发
    onBeforeUpload(file) {
        return file
    },
    // 上传进度的回调函数
    onProgress(progress) {
        console.log('progress', progress) //进度: 0-100
    },
    // 单个文件上传成功之后
    onSuccess(file, res) {
        parent.layer.msg('上传成功', {icon: 1});
    },
    // 单个文件上传失败
    onFailed(file, res) {
        layer.alert(`${res.message}`,{icon:5,title:`上传失败: ${file.name}`,anim: 2,closeBtn: 0});
        console.log(res );
    },
    // 上传错误，或者触发 timeout 超时
     onError(file, err, res) {
        layer.alert(`${err}`,{icon:5,title:`上传错误: ${file.name}`,anim: 2,closeBtn: 0});
    },
}
const editor = createEditor({
    selector: '#editor-container',
    html: $('#content').val(),
    config: editorConfig,
    mode: 'default'
})

const toolbarConfig = {excludeKeys: ['fullScreen','group-video']}

const toolbar = createToolbar({
    editor,
    selector: '#toolbar-container',
    config: toolbarConfig,
    mode: 'default'
})


layui.use(['form'], function () {
    var form = layui.form;

<?php if($mode == 'edit'){ ?>
    form.val('form',{category:<?php echo $data['category'];?>,state:<?php echo $data['state'];?>});
<?php }?>

    var original_md5 = $.md5(JSON.stringify(form.val('form')));

    $('#cancel').click(function () {
        let data = form.val('form');
        if($.md5(JSON.stringify(form.val('form'))) == original_md5){
            parent.layer.close(parent.layer.getFrameIndex(window.name));
            return false;
        }
        layer.confirm('确定取消?',{icon: 3, title:'温馨提示'}, function(index){
            parent.layer.close(parent.layer.getFrameIndex(window.name));
        });
        return false;
    });
    
    $('#save').click(function () {
        let data = form.val('form');
        if(data.title == ''){
            layer.msg('标题不能为空,请输入标题',{icon: 5});
            return false;
        }
        if(data.summary == ''){
            data.summary = truncateString(editor.getText(),120).replace(/\n/g, ' ');
        }
        let loading = layer.msg('正在处理,请稍后..', {icon: 16,time: 1000*300,shadeClose: false});
        $.post(get_api('write_article','save_article'),data,function(data,status){
            layer.close(loading);
            if(data.code == 1) {
                parent.layer.close(parent.layer.getFrameIndex(window.name));
                parent.layer.msg('操作成功', {icon: 1});
            }else{
                layer.msg(data.msg || '未知错误',{icon: 5});
            }
        });
        return false;
    });
});

function truncateString(str,n) {
    var r=/[^\x00-\xff]/g;
    if(str.replace(r,"mm").length<=n){return str;}
    var m=Math.floor(n/2);
    for(var i=m;i<str.length;i++){
        if(str.substr(0,i).replace(r,"mm").length>=n){
            return str.substr(0,i)+"...";
        }
    }
    return str;
}


</script>
</body>
</html>