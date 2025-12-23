<template>
    <div style="padding: 20px; background: white; min-height: 100vh;">
        <h1 style="color: red; font-size: 24px; margin-bottom: 20px;">MOBILE TEST PAGE</h1>
        
        <p style="font-size: 18px; margin-bottom: 10px;">
            Nếu bạn thấy chữ này, Vue đã render thành công trên mobile.
        </p>
        
        <button 
            @click="counter++"
            style="padding: 15px 30px; font-size: 18px; background: blue; color: white; border: none; border-radius: 5px; margin: 10px 0;"
        >
            Click me: {{ counter }}
        </button>
        
        <div style="margin-top: 20px; padding: 15px; background: yellow;">
            <p style="font-size: 16px;">Counter value: {{ counter }}</p>
        </div>
        
        <a href="/patients" style="display: block; margin-top: 20px; font-size: 18px; color: blue;">
            Back to Patients List
        </a>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const counter = ref(0);
</script>
