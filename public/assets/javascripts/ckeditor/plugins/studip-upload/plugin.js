CKEDITOR.plugins.add('studip-upload', {
    icons: 'upload',
    hidpi: true,
    lang: 'de,en',
    init: function(editor){
        var lang = editor.lang['studip-upload'];
        // utilities
        var isString = function(object) {
                return (typeof object) === 'string';
            },
            isImage = function(mime_type){
                return isString(mime_type) && mime_type.match('^image');
            },
            isSVG = function(mime_type){
                return isString(mime_type) && mime_type === 'image/svg+xml';
            },
            insertNode = function($node){
                editor.insertHtml($('<div>').append($node).html() + ' ');
            },
            insertImage = function(file){

                insertNode($('<img />').attr({
                    src: file.url,
                    alt: file.name,
                    title: file.name
                }));
            },
            insertLink = function(file){
                insertNode($('<a>').attr({
                    href: file.url,
                    type: file.type,
                    target: '_blank',
                    rel: 'nofollow'
                }).append(file.name));
            },
            insertFile = function(file){
                // NOTE StudIP sends SVGs as application/octet-stream
                if (isImage(file.type) && !isSVG(file.type)) {
                    insertImage(file);
                } else {
                    insertLink(file);
                }
            },
            converterDataURItoBlob = function(dataURI) {
                let byteString;
                let mimeString;
                let ia;
                if (dataURI.split(',')[0].indexOf('base64') >= 0) {
                    split_one = dataURI.split(',')[1];
                    clean_split = split_one.split('"')[0];
                     byteString = atob(clean_split);
                } else {
                    byteString = encodeURI(dataURI.split(',')[1]);
                }
                // separate out the mime component
                
                mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
            
                // write the bytes of the string to a typed array
                ia = new Uint8Array(byteString.length);
                for (var i = 0; i < byteString.length; i++) {
                ia[i] = byteString.charCodeAt(i);
                }
                return new Blob([ia], {type:mimeString});
            },
            uploadFromBase64 = function(studipUpload_url, formDataToUpload, event, replacerStr = null) {
                $.ajax({
                    url: studipUpload_url,
                    data: formDataToUpload,
                    type:"POST",
                    contentType:false,
                    async: false,
                    processData:false,
                    cache:false,
                    dataType:"json", 
                    error:function(err){
                        console.error(err);
                    },
                    success:function(data){
                        console.log(event);
                        if(isImage(data.files[0].type) && !isSVG(data.files[0].type)){
                            tmp = "<img alt='" + data.files[0].name + "'" + "src='" + data.files[0].url +"'/>";
                            if (replacerStr) {
                                console.log("DO STUFF:" + replacerStr); 
                                console.log("replace with" + tmp);
                                event.data.dataValue = event.data.dataValue.replace(replacerStr, tmp);
                                console.log("dataVal: " + event.data.dataValue);  
                            } else {
                                event.data.dataValue = tmp;
                            }
                        }
                        if (isSVG(data.files[0].type)) {
                            event.data.dataValue = "";
                            //TODO: Link insert
                            alert("Das Einfügen von Svg-Dateien ist verboten.")
                        }
                        
                    },
                    complete:function(){
                        console.log("Request finished.");
                    }
                });
            },
            handleUploads = function(fileList){
                var errors = [];
                $.each(fileList, function(index, file){
                    if (file.url) {
                        insertFile(file);
                    } else {
                        errors.push(file.name + ': ' + file.error);
                    }
                });
                if (errors.length) {
                    alert(lang.uploadError + '\n\n' + errors.join('\n'));
                }
            };

        // actual file upload handler
        // NOTE depends on jQuery File Upload plugin being loaded beforehand!
        // TODO integrate jQuery File Upload plugin into studip-upload
        var inputId = 'fileupload';
        editor.on('instanceReady', function(event){
            var $container = $(event.editor.container.$);
            
            // install upload handler
            $('<input>')
                .attr({
                    id: inputId,
                    type: 'file',
                    name: 'files[]',
                    multiple: true,
                })
                .css('display', 'none')
                .appendTo($container)
                .fileupload({
                    url: editor.config.studipUpload_url,
                    singleFileUploads: false,
                    dataType: 'json',
                    done: function(e, data){
                        if (data.result.files) {

                            handleUploads(data.result.files);
                        } else {
                            alert(lang.uploadFailed + '\n\n' + data.result);
                        }
                    },
                    fail: function (e, data) {
                        alert(
                            lang.uploadFailed + '\n\n'
                            + lang.error + ' '
                            + data.errorThrown.message
                        );
                    }
                });
                
        });

        // avoid multiple uploads of the same file via drag and drop
        editor.on('beforeDestroy', function(event){
           
            if ($.fn.fileupload) {
                $('#' + inputId).fileupload('destroy');
            }
        });

        // ckeditor
        editor.addCommand('upload', {    // command handler
            exec: function(editor){
                // NOTE if  $('#' + inputId) is stored in variable then
                //      upload works only once
                $('#' + inputId).click();
            }
        });
        editor.ui.addButton('upload', {  // toolbar button
            label: lang.buttonLabel,
            command: 'upload',
            toolbar: 'insert,80'
        });

        // editor paste event - to handle copy paste files in wysywig
        editor.on( 'paste', function( event ) {
            // Check for multiple uploads
            if (event.data.dataValue && event.data.dataTransfer._.files.length == 0) {
                //Filter img from editor source-code
                //TODO jquery object from html string
                str = event.data.dataValue;
                re = /<img\s[^>]*?src\s*=\s*['\"]([^'\"]*?)['\"][^>]*?>/g;
                img_filter = (str.match(re));
                console.log(img_filter);
                for (i = 0; i < img_filter.length; i++) {
                    replacerStr = img_filter[i];
                    blob = converterDataURItoBlob(img_filter[i]);
                    formDataToUpload = new FormData();
                    formDataToUpload.append("files[]", blob);
                    uploadFromBase64(editor.config.studipUpload_url, formDataToUpload, event, replacerStr);

                }
            } else {
                // Check browser
                if (CKEDITOR.env.chrome) {
                    console.log(event);
                    var reader = new FileReader();
                    reader.readAsDataURL(event.data.dataTransfer._.files[0]);
                    reader.onload = function () {
                        input = reader.result;
                        console.log(input + "1");
                        blob = converterDataURItoBlob(input);
                        formDataToUpload = new FormData();
                        formDataToUpload.append("files[]", blob);
                        uploadFromBase64(editor.config.studipUpload_url, formDataToUpload, event);
                        console.log(editor.value);
                        editor.insertHtml(event.data.dataValue);
                    } 
                } else {
                    blob = converterDataURItoBlob(event.data.dataValue);
                    formDataToUpload = new FormData();
                    formDataToUpload.append("files[]", blob);
                    uploadFromBase64(editor.config.studipUpload_url, formDataToUpload, event);                 
                }
            }
        }); 
        
    }
});

