// Kazakhstan numbers keep a readable mask; explicit international numbers remain usable.
function formatPhone(raw){
  let digits=raw.replace(/\D/g,'');
  if(!digits)return '';
  if(!raw.trim().startsWith('+')&&digits.length===11&&digits.startsWith('8'))digits='7'+digits.slice(1);
  else if(!raw.trim().startsWith('+')&&digits.length===10)digits='7'+digits;
  else if(!raw.trim().startsWith('+')&&digits.length<11)digits='7'+digits;
  if(!digits.startsWith('7'))return '+'+digits.slice(0,15);
  const national=digits.slice(1,11);
  let formatted='+7 ';
  if(national.length){formatted+='('+national.slice(0,3);if(national.length>=3)formatted+=') ';}
  if(national.length>3)formatted+=national.slice(3,6);
  if(national.length>6)formatted+='-'+national.slice(6,8);
  if(national.length>8)formatted+='-'+national.slice(8,10);
  return formatted;
}
function caretForDigits(value,count){
  if(!count)return 0;
  let seen=0;
  for(let i=0;i<value.length;i++)if(/\d/.test(value[i])&&++seen===count)return i+1;
  return value.length;
}
export function initPhoneMasks(root=document){
  root.querySelectorAll('[data-phone-mask]').forEach(input=>{
    const format=()=>{
      const raw=input.value,caret=input.selectionStart??raw.length;
      const end=caret===raw.length,count=raw.slice(0,caret).replace(/\D/g,'').length;
      input.value=formatPhone(raw);
      const position=end?input.value.length:caretForDigits(input.value,count);
      input.setSelectionRange(position,position);
    };
    input.addEventListener('focus',()=>{if(!input.value){input.value='+7 ';input.setSelectionRange(3,3);}});
    input.addEventListener('blur',()=>{if(input.value.replace(/\D/g,'')==='7')input.value='';});
    input.addEventListener('input',event=>{if(!event.isComposing)format();});
    input.addEventListener('compositionend',format);
    input.addEventListener('beforeinput',event=>{
      const start=input.selectionStart,end=input.selectionEnd;
      if(!['deleteContentBackward','deleteContentForward'].includes(event.inputType)||start!==end)return;
      const backward=event.inputType==='deleteContentBackward';
      if(input.value.startsWith('+7')&&((backward&&start===2)||(!backward&&start===1))){event.preventDefault();input.setSelectionRange(3,3);return;}
      let index=backward?start-1:start;
      if(index<0||index>=input.value.length||/\d/.test(input.value[index]))return;
      while(index>=0&&index<input.value.length&&!/\d/.test(input.value[index]))index+=backward?-1:1;
      if(index<0||index>=input.value.length)return;
      event.preventDefault();
      if(index===1&&input.value.startsWith('+7')&&input.value.replace(/\D/g,'').length>1){input.setSelectionRange(3,3);return;}
      input.value=index===1&&input.value.startsWith('+7')?'':input.value.slice(0,index)+input.value.slice(index+1);
      input.setSelectionRange(index,index);format();
      input.dispatchEvent(new Event('input',{bubbles:true}));
    });
    if(input.value)format();
  });
}
