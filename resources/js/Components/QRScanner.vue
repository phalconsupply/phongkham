<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Html5Qrcode } from 'html5-qrcode';

const emit = defineEmits(['scan-success', 'scan-error', 'close']);

const scannerRef = ref(null);
const isScanning = ref(false);
const errorMessage = ref('');
const html5QrCode = ref(null);
const devices = ref([]);
const selectedDeviceId = ref(null);
const scanMode = ref('camera'); // 'camera' or 'file'
const permissionRequested = ref(false);
const isInitializing = ref(false);

// Parse CCCD QR Code data
const parseCCCDData = (qrData) => {
    try {
        // CCCD QR format: ID|FULLNAME|DOB|GENDER|ADDRESS|ISSUE_DATE
        // Example: 001234567890|NGUYEN VAN A|01011990|Nam|123 Duong ABC, Phuong XYZ|01012020
        const parts = qrData.split('|');
        
        if (parts.length < 5) {
            throw new Error('Invalid CCCD QR format');
        }

        // Parse date of birth (DDMMYYYY or DD/MM/YYYY)
        let dob = parts[2].replace(/\//g, '');
        if (dob.length === 8) {
            // Convert DDMMYYYY to YYYY-MM-DD
            const day = dob.substring(0, 2);
            const month = dob.substring(2, 4);
            const year = dob.substring(4, 8);
            dob = `${year}-${month}-${day}`;
        }

        // Parse gender
        let gender = 'other';
        const genderText = parts[3].toLowerCase();
        if (genderText.includes('nam') || genderText === 'male') {
            gender = 'male';
        } else if (genderText.includes('nữ') || genderText === 'female' || genderText.includes('nu')) {
            gender = 'female';
        }

        // Split full name into first and last name
        const fullName = parts[1].trim();
        const nameParts = fullName.split(' ');
        const firstName = nameParts.pop() || '';
        const lastName = nameParts.join(' ') || '';

        return {
            id_number: parts[0].trim(),
            full_name: fullName,
            last_name: lastName,
            first_name: firstName,
            date_of_birth: dob,
            gender: gender,
            address: parts[4] ? parts[4].trim() : '',
        };
    } catch (error) {
        console.error('Error parsing CCCD data:', error);
        throw new Error('Không thể đọc thông tin từ mã QR. Vui lòng kiểm tra lại.');
    }
};

// Initialize camera
const initCamera = async () => {
    try {
        errorMessage.value = '';
        isInitializing.value = true;
        
        // Request camera permission explicitly
        if (!permissionRequested.value) {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                // Stop the stream immediately, we just needed permission
                stream.getTracks().forEach(track => track.stop());
                permissionRequested.value = true;
            } catch (permError) {
                console.error('Camera permission error:', permError);
                errorMessage.value = 'Vui lòng cấp quyền truy cập camera trong cài đặt trình duyệt.';
                isInitializing.value = false;
                return;
            }
        }
        
        // Get available cameras
        const devicesInfo = await Html5Qrcode.getCameras();
        devices.value = devicesInfo;
        
        if (devicesInfo && devicesInfo.length > 0) {
            // Prefer back camera on mobile
            const backCamera = devicesInfo.find(device => 
                device.label.toLowerCase().includes('back') || 
                device.label.toLowerCase().includes('rear') ||
                device.label.toLowerCase().includes('environment')
            );
            selectedDeviceId.value = backCamera ? backCamera.id : devicesInfo[0].id;
            
            await startScanning();
        } else {
            errorMessage.value = 'Không tìm thấy camera. Vui lòng cho phép truy cập camera.';
        }
        
        isInitializing.value = false;
    } catch (error) {
        console.error('Error initializing camera:', error);
        errorMessage.value = 'Không thể khởi động camera. Vui lòng kiểm tra quyền truy cập camera.';
        isInitializing.value = false;
    }
};

// Start scanning
const startScanning = async () => {
    try {
        if (!html5QrCode.value) {
            html5QrCode.value = new Html5Qrcode('qr-reader');
        }

        const config = {
            fps: 10,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0,
        };

        await html5QrCode.value.start(
            selectedDeviceId.value,
            config,
            onScanSuccess,
            onScanError
        );

        isScanning.value = true;
    } catch (error) {
        console.error('Error starting scanner:', error);
        errorMessage.value = 'Lỗi khi bắt đầu quét: ' + error.message;
    }
};

// Handle successful scan
const onScanSuccess = (decodedText, decodedResult) => {
    try {
        const cccdData = parseCCCDData(decodedText);
        emit('scan-success', cccdData);
        stopScanning();
    } catch (error) {
        errorMessage.value = error.message;
        emit('scan-error', error.message);
    }
};

// Handle scan error (silent)
const onScanError = (error) => {
    // Ignore scan errors (no QR in frame)
};

// Stop scanning
const stopScanning = async () => {
    try {
        if (html5QrCode.value && isScanning.value) {
            await html5QrCode.value.stop();
            isScanning.value = false;
        }
    } catch (error) {
        console.error('Error stopping scanner:', error);
    }
};

// Handle file upload
const handleFileUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    try {
        errorMessage.value = '';
        
        if (!html5QrCode.value) {
            html5QrCode.value = new Html5Qrcode('qr-reader');
        }

        const result = await html5QrCode.value.scanFile(file, true);
        const cccdData = parseCCCDData(result);
        emit('scanned', cccdData);
    } catch (error) {
        console.error('Error scanning file:', error);
        errorMessage.value = 'Không thể đọc mã QR từ ảnh. Vui lòng thử lại.';
        emit('error', error.message);
    }
};

// Switch camera
const switchCamera = async () => {
    if (devices.value.length <= 1) return;
    
    await stopScanning();
    
    const currentIndex = devices.value.findIndex(d => d.id === selectedDeviceId.value);
    const nextIndex = (currentIndex + 1) % devices.value.length;
    selectedDeviceId.value = devices.value[nextIndex].id;
    
    await startScanning();
};

// Close modal
const close = async () => {
    await stopScanning();
    emit('close');
};

// Lifecycle hooks
onMounted(() => {
    initCamera();
});

onBeforeUnmount(() => {
    stopScanning();
});
</script>

<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4">
        <div class="relative w-full max-w-2xl rounded-lg bg-white p-4 sm:p-6 shadow-xl">
            <!-- Header -->
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">
                    Quét Mã QR Căn Cước
                </h3>
                <button
                    @click="close"
                    class="rounded-lg p-2 text-gray-400 hover:bg-gray-200 hover:text-gray-900"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Initializing Message -->
            <div v-if="isInitializing" class="mb-4 rounded-lg bg-blue-100 p-4 text-center">
                <p class="text-blue-800">Đang yêu cầu quyền truy cập camera...</p>
            </div>

            <!-- Error Message -->
            <div v-if="errorMessage" class="mb-4 rounded-lg bg-red-100 p-3 text-sm text-red-800">
                {{ errorMessage }}
            </div>

            <!-- Scanner Area -->
            <div class="relative">
                <!-- QR Reader Container -->
                <div id="qr-reader" class="rounded-lg overflow-hidden"></div>

                <!-- Controls -->
                <div v-if="isScanning && devices.length > 1" class="mt-4 flex justify-center">
                    <button
                        @click="switchCamera"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                    >
                        🔄 Đổi Camera
                    </button>
                </div>

                <!-- Instructions -->
                <div class="mt-4 text-center text-sm text-gray-600">
                    <p>📱 Đưa mã QR trên Căn Cước vào khung quét</p>
                    <p class="mt-2 text-xs text-gray-500">Camera sẽ tự động quét khi phát hiện mã QR</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
#qr-reader {
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
}

#qr-reader video {
    width: 100%;
    border-radius: 0.5rem;
}
</style>
