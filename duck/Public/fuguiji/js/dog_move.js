!function dog(){
				var eX,eY;
				var dogX,dogY;
				var touchInnerX,touchInnerY;
				var dog = document.querySelector('.dog');
				var dogWidth = dog.offsetWidth;
				var dogHeight= dog.offsetHeight;
				 dogWidth = $('.dog').width();
				 dogHeight= $('.dog').height();
				var dogMaxX,dogMinY=document.querySelector('.vine').offsetHeight,dogMaxY=document.querySelector('.desktop').offsetTop-dogHeight;
				dog.style.top = dogMaxY+'px';

				dog.addEventListener('touchstart',touch, false);
				dog.addEventListener('touchmove',touch, false);
				dog.addEventListener('touchend',touch, false);

				function touch (event){
					var event = event || window.event;

					switch(event.type){
						case "touchstart":
							event.preventDefault();
							eX = event.touches[0].clientX;
							eY = event.touches[0].clientY;
							dogX = dog.offsetLeft;
							dogY = dog.offsetTop;
							touchInnerX = eX-dogX;
							touchInnerY = eY-dogY;
							dogMaxX = $('body').width()-(dogWidth)
							break;
						case "touchend":
							event.preventDefault();
							//event.changedTouches[0].clientX;event.changedTouches[0].clientY;
							if(dog.offsetLeft < ($('body').width()-dogWidth)/2){
								$(dog).animate({'left':0},300);
							}else{
								$(dog).animate({'left':dogMaxX},300);
							}
							break;
						case "touchmove":
							event.preventDefault();
							dogX = event.touches[0].clientX - touchInnerX;
							dogY = event.touches[0].clientY - touchInnerY;

							if(dogX>dogMaxX){
								dog.style.left = dogMaxX+'px';
							}else if(dogX<0){
								dog.style.left = 0;
							}else {
								dog.style.left = dogX+'px';
							}

							if(dogY<dogMinY){
								dog.style.top = dogMinY+'px';
							}else if(dogY>dogMaxY){
								dog.style.top = dogMaxY+'px';
							}else{
								dog.style.top = dogY+'px';
							}

							break;
					}
				}
			}();