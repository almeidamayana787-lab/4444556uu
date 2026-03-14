<template>
  <div class="flex flex-col min-h-screen bg-[#1a1a1a] text-white pb-24">
    <!-- Top Banner -->
    <div class="relative w-full">
      <img src="/casino_icons/bannerganhereg.avif" alt="Promo" class="w-full h-auto" />
      <button @click="$emit('close')" class="absolute top-4 left-4 p-2 bg-black/40 rounded-full">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>
    </div>

    <!-- Tabs -->
    <div class="flex border-b border-gray-800 px-6 mt-4">
      <button class="flex-1 pb-3 text-sm font-bold border-b-2 border-[#fca000] text-[#fca000]">Registro</button>
      <button class="flex-1 pb-3 text-sm font-bold text-gray-500">Login</button>
    </div>

    <!-- Form -->
    <div class="p-6 space-y-6">
      <!-- Phone/Account Input -->
      <div class="space-y-2">
        <label class="text-[11px] text-gray-400 font-medium tracking-wide">Suporte Número do Celular/E-mail/Conta Registro</label>
        <div class="flex items-center bg-[#121212] rounded-lg border border-gray-800 h-12 px-3 focus-within:border-[#fca000]/50 transition-colors">
          <div class="flex items-center space-x-2 pr-2 border-r border-gray-800">
            <img src="/casino_icons/brasilband.png" alt="BR" class="w-6 h-4 object-contain" />
            <span class="text-sm font-medium text-gray-300">+55</span>
          </div>
          <input 
            type="text" 
            placeholder="*Digite o Número do Celular/E-mail/Conta" 
            class="bg-transparent flex-1 outline-none px-3 text-sm placeholder:text-gray-600"
          />
        </div>
      </div>

      <!-- Password Input -->
      <div class="space-y-2">
        <div class="flex items-center justify-between">
          <label class="flex items-center text-[11px] text-[#fca000] font-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
            </svg>
            Cadastro de senha
          </label>
          <span class="flex items-center text-[10px] text-gray-500">
             <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m0 11v1m0-11a8.5 8.5 0 013.892 16.072M12 4a8.5 8.5 0 00-3.892 16.072" />
             </svg>
             Cadastro com código
          </span>
        </div>
        <div class="bg-[#121212] rounded-lg border border-gray-800 h-12 px-3 flex items-center focus-within:border-[#fca000]/50 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
          <input 
            type="password" 
            placeholder="*Insira a senha" 
            class="bg-transparent flex-1 outline-none px-3 text-sm placeholder:text-gray-600"
          />
          <button class="text-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Password Strength -->
      <div class="space-y-2">
        <label class="text-[11px] text-gray-500">Força</label>
        <div class="flex space-x-2">
          <div v-for="i in 4" :key="i" class="h-1 flex-1 bg-gray-800 rounded-full"></div>
        </div>
      </div>

      <!-- Confirm Password -->
      <div class="bg-[#121212] rounded-lg border border-gray-800 h-12 px-3 flex items-center focus-within:border-[#fca000]/50 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
        <input 
          type="password" 
          placeholder="*Confirmar senha" 
          class="bg-transparent flex-1 outline-none px-3 text-sm placeholder:text-gray-600"
        />
        <button class="text-gray-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
          </svg>
        </button>
      </div>

      <!-- Agreement -->
      <div class="flex items-start space-x-3">
        <div class="w-5 h-5 rounded bg-green-600 flex items-center justify-center shrink-0 mt-0.5">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
             <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
           </svg>
        </div>
        <p class="text-[11px] text-white leading-tight">
          Tenho mais de anos, li e concordo com <span class="text-[#fca000]">«Acordo de Usuário»</span>
        </p>
      </div>

      <!-- Auth Button With Gift Icon -->
      <div class="relative w-full mt-6">
        <button @click="handleRegister" :disabled="isLoading" class="w-full bg-gradient-to-r from-yellow-500 to-yellow-600 text-black font-bold py-3 rounded-xl shadow-lg hover:brightness-110 transition flex justify-center items-center">
          <span v-if="isLoading">Registrando...</span>
          <span v-else>{{ isLoginMode ? 'Login' : 'Registro' }}</span>
        </button>
        <img v-if="!isLoginMode" src="/casino_icons/presente.avif" alt="Gift" class="absolute -top-6 left-1/2 transform -translate-x-1/2 w-10 animate-bounce pointer-events-none drop-shadow-lg" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
const emit = defineEmits(['close', 'register-success']);

const isLoginMode = ref(false);
const showPassword = ref(false);
const formPhone = ref('');
const formPassword = ref('');
const isLoading = ref(false);

const handleRegister = async () => {
  if (isLoginMode.value) return; // Mock login logic for now
  if (!formPhone.value || !formPassword.value) return;
  
  isLoading.value = true;
  try {
    const res = await fetch('/api/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        phone: formPhone.value,
        password: formPassword.value
      })
    });
    
    if (res.ok) {
      const data = await res.json();
      emit('register-success', data.user);
    } else {
      console.error("Error registering", await res.text());
    }
  } catch (err) {
    console.error(err);
  } finally {
    isLoading.value = false;
  }
};
</script>
