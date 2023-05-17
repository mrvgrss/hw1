function menuClick(event){
    if(userLoggedInMeta && userLoggedInMeta.getAttribute('content') === 'false'){
        return;
    }
    if(isShowingMenu){
        isShowingMenu = false;
        menu.style.display = 'none';
    }else{
        isShowingMenu = true;
        menu.style.display = 'block';
    }
    event.stopPropagation();
}
function documentClick(event){
    if(isShowingMenu && ((event.target != menu && !menu.contains(event.target)) || (event.target != userInfo && !userInfo.contains(event.target)))){
        menu.style.display = 'none';
        isShowingMenu = false;
    }
}
const userLoggedInMeta = document.querySelector('meta[name="userLoggedIn"]');
const menu = document.querySelector('#menu');
const userInfo = document.querySelector('#user-info');
let isShowingMenu = false;
if (userLoggedInMeta && userLoggedInMeta.getAttribute('content') === 'false') {
  document.querySelector('#logged-id-menu-buttons').style.display = 'none';
  document.querySelector('#logout').style.display = 'none';
}

menu.style.display = 'none';
document.addEventListener('click', documentClick);
userInfo.addEventListener('click', menuClick);
