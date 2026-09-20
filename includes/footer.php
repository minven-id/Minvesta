</main></div>
<footer class="footer-neon">
  <div class="footer-content">
    <span class="footer-text">© <?= date('Y') ?> Minven.Id</span>
    <span class="footer-divider">|</span>
    <span class="footer-text">Bank Sampah Management System</span>
  </div>
</footer>
<style>
.footer-neon{
  background:linear-gradient(180deg,rgba(22,101,52,.05),transparent);
  border-top:1px solid rgba(34,197,94,.15);
  padding:20px 0;
  margin-top:40px;
  text-align:center;
}
.footer-content{
  display:flex;
  align-items:center;
  justify-content:center;
  gap:12px;
  font-size:12px;
  color:var(--c-ink-400);
  font-weight:600;
  letter-spacing:.3px;
}
.footer-text{
  animation:neonGlow 2s ease-in-out infinite alternate;
}
.footer-divider{
  color:rgba(34,197,94,.3);
  animation:neonDivider 2s ease-in-out infinite alternate;
}
@keyframes neonGlow{
  0%{
    text-shadow:0 0 5px rgba(34,197,94,.3),0 0 10px rgba(34,197,94,.2);
    color:var(--c-ink-400);
  }
  100%{
    text-shadow:0 0 10px rgba(34,197,94,.6),0 0 20px rgba(34,197,94,.4),0 0 30px rgba(34,197,94,.3);
    color:var(--c-sage-700);
  }
}
@keyframes neonDivider{
  0%{
    opacity:.3;
    text-shadow:0 0 5px rgba(34,197,94,.2);
  }
  100%{
    opacity:.8;
    text-shadow:0 0 10px rgba(34,197,94,.5),0 0 15px rgba(34,197,94,.3);
  }
}
@media(max-width:640px){
  .footer-content{
    flex-direction:column;
    gap:8px;
  }
  .footer-divider{
    display:none;
  }
}
</style>
<script>
const sidebar=document.querySelector('.sidebar');
const menuToggle=document.querySelector('.menu-toggle');
const sidebarOverlay=document.getElementById('sidebar-overlay');
if(sidebar) sidebar.id='app-sidebar';
if(!sidebarOverlay){
	const navBackdrop=document.createElement('button');
	navBackdrop.type='button';
	navBackdrop.className='nav-backdrop';
	navBackdrop.id='sidebar-overlay';
	navBackdrop.setAttribute('aria-label','Tutup menu navigasi');
	document.body.appendChild(navBackdrop);
}
document.querySelectorAll('.nav-label').forEach((label,index)=>{
	const items=[];
	let sibling=label.nextElementSibling;
	while(sibling&&!sibling.classList.contains('nav-label')){
		sibling.classList.add('nav-child');
		items.push(sibling);
		sibling=sibling.nextElementSibling;
	}
	const groupId=`nav-group-${index+1}`;
	const group=document.createElement('div');
	group.className='nav-dropdown';
	group.id=groupId;
	label.parentNode.insertBefore(group,items[0]);
	items.forEach(item=>group.appendChild(item));
	label.id=`${groupId}-label`;
	label.setAttribute('aria-controls',groupId);
	const hasActive=items.some(item=>item.matches('.active')||item.querySelector('.active'));
	const setGroup=(open)=>{
		label.setAttribute('aria-expanded',open?'true':'false');
		label.classList.toggle('is-open',open);
		group.hidden=!open;
		items.forEach(item=>item.classList.toggle('nav-child-visible',open));
	};
	setGroup(hasActive);
	label.addEventListener('click',()=>setGroup(label.getAttribute('aria-expanded')!=='true'));
});
if(menuToggle&&sidebar){
	const setSidebar=(open)=>{
		sidebar.classList.toggle('is-open',open);
		menuToggle.setAttribute('aria-expanded',open?'true':'false');
		document.body.classList.toggle('nav-open',open);
		const overlay=document.getElementById('sidebar-overlay');
		if(overlay) overlay.classList.toggle('is-open',open);
	};
	menuToggle.addEventListener('click',()=>setSidebar(!sidebar.classList.contains('is-open')));
	const overlay=document.getElementById('sidebar-overlay');
	if(overlay) overlay.addEventListener('click',()=>setSidebar(false));
	sidebar.querySelectorAll('a').forEach(link=>link.addEventListener('click',()=>{
		setSidebar(false);
	}));
	document.addEventListener('keydown',event=>{
		if(event.key==='Escape'&&sidebar.classList.contains('is-open')) setSidebar(false);
	});
}
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
document.querySelectorAll('.card.form:not(dialog), .wallet-create:not(dialog)').forEach((formCard,index)=>{
	const modalId=formCard.id||`form-modal-${index+1}`;
	formCard.id=modalId;
	const modal=document.createElement('dialog');
	modal.className='app-form-modal';
	modal.id=modalId;
	modal.innerHTML='<div class="modal-shell"></div>';
	modal.querySelector('.modal-shell').append(...Array.from(formCard.childNodes));
	formCard.replaceWith(modal);
	const title=modal.querySelector('h2')?.textContent?.trim()||document.querySelector('.section-head h1')?.textContent?.trim()||'Formulir';
	if(!modal.querySelector('.modal-close')){
		const close=document.createElement('button');
		close.type='button'; close.className='modal-close'; close.dataset.modalClose=''; close.setAttribute('aria-label','Tutup dialog'); close.innerHTML='&times;';
		const heading=modal.querySelector('h2');
		if(heading) heading.parentNode.insertBefore(close,heading.nextSibling);
		else modal.querySelector('.modal-shell').insertAdjacentHTML('afterbegin',`<div class="modal-form-heading"><span class="eyebrow">Input data</span><h2>${title}</h2><button type="button" class="modal-close" data-modal-close aria-label="Tutup dialog">&times;</button></div>`);
	}
	modal.querySelectorAll('[data-modal-close]').forEach(close=>close.addEventListener('click',()=>modal.close()));
	modal.addEventListener('click',event=>{if(event.target===modal) modal.close()});
	const trigger=document.querySelector(`[href="#${CSS.escape(modalId)}"]`);
	if(trigger) trigger.href='#';
	if(trigger) trigger.addEventListener('click',event=>{event.preventDefault();modal.showModal()});
	if(new URLSearchParams(location.search).has('edit')) modal.showModal();
});
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
