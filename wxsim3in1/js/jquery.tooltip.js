this.tooltip=function(){xOffset=30;yOffset=20;
$(".tooltip").hover(function(a){this.t = $(this).attr("title");this.title="";
$("<div id='tooltip'>"+this.t+"</div>").appendTo("body").css({top:(a.pageY-xOffset)+"px",left:(a.pageX+yOffset)+"px"}).fadeIn(200)},
function(){this.title=this.t;$("#tooltip").remove();}
);
$(".tooltip").mousemove(function(a){$("#tooltip").css({top:(a.pageY-xOffset)+"px",left:(a.pageX+yOffset)+"px"});});
};