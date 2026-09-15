</main></div>
<script>
const sidebar=document.querySelector('.sidebar');
const menuToggle=document.querySelector('.menu-toggle');
if(sidebar) sidebar.id='app-sidebar';
document.querySelectorAll('.nav-label').forEach((label,index)=>{
	const items=[];
	let sibling=label.nextElementSibling;
	while(sibling&&!sibling.classList.contains('nav-label')){
		sibling.classList.add('nav-child');
		items.push(sibling);
		sibling=sibling.nextElementSibling;
	}
	const groupId=`nav-group-${index+1}`;
	label.id=`${groupId}-label`;
	label.setAttribute('aria-controls',groupId);
	items.forEach(item=>item.setAttribute('data-nav-group',groupId));
	const hasActive=items.some(item=>item.matches('.active')||item.querySelector('.active'));
	const setGroup=(open)=>{
		label.setAttribute('aria-expanded',open?'true':'false');
		label.classList.toggle('is-open',open);
		items.forEach(item=>{
			item.hidden=!open;
			item.classList.toggle('nav-child-visible',open);
		});
	};
	setGroup(hasActive);
	label.addEventListener('click',()=>setGroup(label.getAttribute('aria-expanded')!=='true'));
});
if(menuToggle&&sidebar){
	menuToggle.addEventListener('click',()=>{
		const open=sidebar.classList.toggle('is-open');
		menuToggle.setAttribute('aria-expanded',open?'true':'false');
		document.body.classList.toggle('nav-open',open);
	});
	sidebar.querySelectorAll('a').forEach(link=>link.addEventListener('click',()=>{
		sidebar.classList.remove('is-open');
		menuToggle.setAttribute('aria-expanded','false');
		document.body.classList.remove('nav-open');
	}));
}
document.addEventListener('keydown',event=>{
	if(event.key==='Escape'&&sidebar?.classList.contains('is-open')){
		sidebar.classList.remove('is-open');
		menuToggle?.setAttribute('aria-expanded','false');
		document.body.classList.remove('nav-open');
	}
});
if(document.body.dataset.liveRefresh==='true'){
	// Keep the session alive without reloading the page or disturbing idle users.
	const keepalive=()=>{
		if(document.visibilityState!=='visible') return;
		fetch(window.location.href,{method:'GET',cache:'no-store',credentials:'same-origin',headers:{'X-Session-Keepalive':'1'}}).catch(()=>{});
	};
	window.setInterval(keepalive,300000);
}
document.querySelectorAll('[data-confirm]').forEach(x=>x.addEventListener('click',e=>{
 if(!confirm(x.dataset.confirm)) e.preventDefault();
}));
document.querySelectorAll('[data-modal-target]').forEach(trigger=>trigger.addEventListener('click',()=>{
	const modal=document.getElementById(trigger.dataset.modalTarget);
	if(modal?.showModal) modal.showModal();
}));
document.querySelectorAll('[data-modal-close]').forEach(close=>close.addEventListener('click',()=>{
	close.closest('dialog')?.close();
}));
document.querySelectorAll('dialog').forEach(modal=>modal.addEventListener('click',event=>{
	if(event.target===modal) modal.close();
}));
</script>
</body></html>
