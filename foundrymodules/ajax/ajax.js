/**
 * User: sanjeev
 * Date: 1/8/14
 * Time: 5:50 PM
 * Ajax module for RequireJS. This is for Joomla and requires a responder
 */

define(['jquery','foundry/url','foundry/dialog'],function(jq,URL,boot){
    'use strict';

    var me;
    var returnObj;
    jq.ajaxSetup({

        headers:{'FOUNDRY_AJAX': 'FoundryAjax'},
        dataType:'JSON',

        fail:function(){
            console.log('Ajax request Failed');
        },beforeSend:function(e){

            var event = jq.Event( "foundry.ajax.start" );

            jq( document ).trigger( event );


        },complete:function(e){

            var event = jq.Event( "foundry.ajax.completed" );

            jq( document ).trigger( event );


        }


    });

    return {
        returnObj:null,
        callback:null,
        progresscallback:null,
        call:function(component,action,options,callback,progresscallback){

            me=this;
            me.callback=callback;
            me.progresscallback=progresscallback;

            var url =  URL(foundryapp);
            //console.log(url);

            var call_ops=[
            ['option','com_'+component],
            ['task',action],
                ['format','json']

            ];

/*
*
* */
        if('undefined'!=options)
            {
                for(var x in options)
                {
                    call_ops.push(new Array(x,options[x]));

                }
            }

            url.query(call_ops);
            url.path('index.php');
            jq.ajax({url:url.toString(),type:'GET'}).done(function(data){

                me.parseResult(data);
                if(typeof(me.callback)=='function')
                {
                    //me.callback.call(this,data);
                }
                    //this.trigger('foundryAjaxStart',this)


                }).fail(function(data){

                    me.parseBSAlert({msg:'Ajax Error'});
                })
            ;


        },

        post:function(component,action,options,data)
        {
            me=this;
            var url =  URL(foundryapp);

            var call_ops=[
                ['option','com_'+component],
                ['task',action],
                ['format','json']

            ];

            if('undefined'!=options)
            {
                for(var x in options)
                {
                    call_ops.push(new Array(x,options[x]));

                }
            }


            url.query(call_ops);
            url.path('index.php');


            jq.ajax({url:url.toString(),type:'POST',data:data}).done(function(data){

               me.parseResult(data);
                if(typeof(me.callback)=='function')
                {
                  //  me.callback.call(this,data);
                }


            }).fail(function(data){

                    me.parseBSAlert({msg:'Ajax Error'});
                })
            ;

        },
        parseResult:function(result)
        {
           if(result.length)
           {
                for(var x in result)
                {

                    var rep=result[x];
                    switch(rep.type)
                    {
                        case 'err':
                            this.parseError(rep.data);
                            break;

                        case 'alt':
                            this.parseAlert(rep.data);
                            break;

                        case 'abl':
                            this.parseBSAlert(rep.data);
                            break;
                        case 'inf':
                            this.parseInfo(rep.data);
                            break;
                        case 'con':

                            this.parseConfirm(rep.data);
                           break;
                        case 'scl':
                            this.parseScript(rep.data);
                            break;
                        case 'rep':
                            this.parseRedirect(rep.data,false)
                            break;
                        case 'bin':
                            this.parseBind(rep.data);
                            break;
                        case 'mod':
                            this.parseModal(rep.data);
                            break;
                        case 'cot':
                            this.parseContent(rep.data);
                            break;
                        case 'inv':
                            this.parseInvoke(rep.data);
                            break;


                    }

                }
           }
        },
        parseScript:function(data)
        {
            if(data.script.ui)
            {


                var ui=data.script.ui;

                for(var x in ui)
                {
                    eval('var '+x+'=require("'+ui[x]+'");');;
                }

                eval(data.script.func);


            }
            else

            {
                if(typeof(data.script)==='function')
                {
                    data.script.call();
                }
                else
                {
                    eval(data.script);

                }


            }



        },
        parseAlert:function(data)
        {
            alert(data.msg);

        },
        parseContent:function(data)
        {
            me.callback.apply(me,new Array(data.content));
            return data.content;

        },
        parseConfirm:function(data)
        {
            if(confirm(data.msg))
            {
                //@todo fix
                eval('abc='+data.payload);
            }
        },
        parseError:function(data)
        {
                boot.alert(data.title,data.msg,'danger',data.element);

        },
        parseBSAlert:function(data)
        {
                boot.alert(data.msg,data.callback);
        },
        parseInfo:function(data)
        {
                boot.info(data.title,data.msg,data.type,data.element);

        }
        ,parseRedirect:function(data,parent){

            location.href=data.url;


        },
        parseInvoke:function(data){

            if(data.func)
            {

                try
                {
                    eval(data.func+'('+data.data+');');
                }
                catch(e)
                {
                    console.log('Not Invokable');
                }


            }

        },
        parseBind:function(data)
        {
            if(data.elements)
            {
                for(var x in data.elements)
                {
                    try
                    {
                        var elem=jq(x);
                        switch(elem.prop('tagName').toLowerCase())
                        {
                            case 'input':
                            case 'button':
                                elem.val(data.elements[x]);
                                break;

                            default:
                                elem.html(data.elements[x]);
                                break;

                        }
                    }
                    catch(e)
                    {

                    }

                }
            }
        },
        parseModal:function(data)
        {

                var types = {'default':boot.TYPE_DEFAULT,
                    'info':boot.TYPE_INFO,
                    'primary':boot.TYPE_PRIMARY,
                    'success':boot.TYPE_SUCCESS,
                    'warning':boot.TYPE_WARNING,
                    'danger':boot.TYPE_DANGER};

                if(data.action.length)
                {
                    for(var d in data.action)
                    {

                        if(typeof(data.action[d].action)!=="undefined")
                        {
                            switch(data.action[d].action)
                            {
                                case 'close':
                                    data.action[d].action=function(d){d.close()};
                                    break;

                            }

                        }


                    }
                }

                var msg=jq(data.body);
                boot.show({
                    title:data.title,
                    message: msg,
                    buttons: data.action
                });
               // boot.show(,data.title,data.action.join(' '));


        }
    };
})