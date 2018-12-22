//bootstrap select
$(function(){
	//初始默认值
	$(".selectpicker").each(function(){
		var select_default = $(this).attr("select_default");
		var my = $(this).parents(".bootstrap-select");
		var url = my.find("select").attr("select_target");
		if(url){
			my.find(".selectpicker").html('');
			getSearchSelectList("",url,my,select_default);
		}
	})
	//点击按钮加载前几个数据作为默认
	$("body").on("click",".bootstrap-select button",function(){
		var my = $(this).parents(".bootstrap-select");
		var url = my.find("select").attr("select_target");
		if(url){
			my.find(".selectpicker").html('');
			getSearchSelectList("",url,my);
		}
	})
	//输入搜索
	$("body").on("keyup.bs.select",".bs-searchbox input",function(){
		var keyword = $(this).val();
		var my = $(this).parents(".bootstrap-select");
		var url = my.find("select").attr("select_target");
		if(url){
			my.find(".selectpicker").html('');
			getSearchSelectList(keyword,url,my);
		}
	})
})
//ajax实时搜索
function getSearchSelectList(keyword,url,my,select_default){
	//异步	
	$.ajaxSetup({  
		async : false  
	}); 
	$.post(url, {
		keyword:keyword,
		default_id:select_default
	}, function(data) {
		var info = data["data"];
		//清空原有
		my.find("li").remove();
		my.find("ul").css({"min-height":"0px"});
		my.find(".selectpicker").html("");
		my.find(".selectpicker").find(".dropdown-menu").children(".inner").css({"min-height":"0px","max-height":"0px"});
		if(info != null &&info.length > 0){
			var selected1 = "";
			if(!select_default){
				selected1 = "selected";
			}
			my.find(".selectpicker").append($("<option value='' "+selected1+">请选择</option>"))
			for(var i = 0;i<info.length;i++){
				var selected = "";
				var active = "";
				if(select_default){
					if(info[i]['id'] == select_default){
						selected = "selected";
						active = "active";
					}
				}
				my.find(".selectpicker").append($("<option value='"+info[i]['id']+"' "+selected+">"+info[i]['name']+"</option>"));
				try{
					my.find(".selectpicker").selectpicker("refresh");
				}catch(e){
					my.find("ul").append('<li class="'+active+'"><a role="option" aria-disabled="false" tabindex="0" aria-selected="false"><span class="glyphicon glyphicon-ok check-mark"></span><span class="text">'+info[i]['name']+'</span></a></li>');
				}
			}
			try{
				if(select_default){
					my.find(".selectpicker").selectpicker("val",select_default);
				}else{
					my.find(".selectpicker").selectpicker("val","");
				}
				my.find(".selectpicker").selectpicker("refresh");
			}catch(e){
				my.find("ul").prepend('<li class="active"><a role="option" aria-disabled="false" tabindex="0" aria-selected="false"><span class="glyphicon glyphicon-ok check-mark"></span><span class="text">请选择</span></a></li>');
			}
		}
	})
}