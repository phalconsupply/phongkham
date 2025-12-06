<template>
    <div class="relative">
        <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        
        <div class="relative">
            <input
                ref="input"
                type="text"
                v-model="searchQuery"
                @input="onInput"
                @focus="showDropdown = true"
                @blur="onBlur"
                @keydown.down.prevent="navigateDown"
                @keydown.up.prevent="navigateUp"
                @keydown.enter.prevent="selectHighlighted"
                @keydown.escape="closeDropdown"
                :placeholder="placeholder"
                :disabled="disabled"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                :class="{ 'border-red-300': hasError }"
            />
            
            <div v-if="loading" class="absolute right-3 top-3">
                <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            
            <!-- Dropdown -->
            <div
                v-if="showDropdown && filteredResults.length > 0"
                class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
            >
                <div
                    v-for="(result, index) in filteredResults"
                    :key="result.id"
                    @mousedown.prevent="selectResult(result)"
                    @mouseenter="highlightedIndex = index"
                    class="cursor-pointer select-none relative py-2 pl-3 pr-9"
                    :class="{
                        'bg-indigo-600 text-white': index === highlightedIndex,
                        'text-gray-900': index !== highlightedIndex
                    }"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <span class="font-semibold">{{ result.code }}</span>
                            <span class="ml-2">{{ result.name_vi }}</span>
                        </div>
                        <div class="flex gap-1 ml-2">
                            <span
                                v-if="result.gender !== 'both'"
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                :class="index === highlightedIndex ? 'bg-indigo-500 text-white' : 'bg-blue-100 text-blue-800'"
                            >
                                {{ result.gender_label }}
                            </span>
                            <span
                                v-if="result.is_chronic"
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                :class="index === highlightedIndex ? 'bg-indigo-500 text-white' : 'bg-yellow-100 text-yellow-800'"
                            >
                                Mãn tính
                            </span>
                            <span
                                v-if="result.reportable"
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                :class="index === highlightedIndex ? 'bg-indigo-500 text-white' : 'bg-red-100 text-red-800'"
                            >
                                Báo cáo
                            </span>
                        </div>
                    </div>
                    <div v-if="result.name_en" class="text-xs mt-1 opacity-75">
                        {{ result.name_en }}
                    </div>
                </div>
            </div>
            
            <!-- No results message -->
            <div
                v-if="showDropdown && searchQuery.length >= 2 && !loading && filteredResults.length === 0"
                class="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md py-3 text-base ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
            >
                <div class="px-3 py-2 text-gray-500 text-center">
                    Không tìm thấy mã ICD-10 phù hợp
                </div>
            </div>
        </div>
        
        <!-- Selected value display -->
        <div v-if="selectedCode && !searchQuery" class="mt-2 p-3 bg-gray-50 rounded-md">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="font-semibold text-indigo-600">{{ selectedCode.code }}</div>
                    <div class="text-sm text-gray-700 mt-1">{{ selectedCode.name_vi }}</div>
                    <div v-if="selectedCode.name_en" class="text-xs text-gray-500 mt-1">{{ selectedCode.name_en }}</div>
                    
                    <div class="flex gap-2 mt-2">
                        <span
                            v-if="selectedCode.gender !== 'both'"
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800"
                        >
                            {{ selectedCode.gender_label }}
                        </span>
                        <span
                            v-if="selectedCode.is_chronic"
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800"
                        >
                            Bệnh mãn tính
                        </span>
                        <span
                            v-if="selectedCode.reportable"
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800"
                        >
                            Bệnh báo cáo
                        </span>
                    </div>
                </div>
                <button
                    type="button"
                    @click="clearSelection"
                    class="ml-2 text-gray-400 hover:text-gray-600"
                >
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Validation warnings -->
        <div v-if="validationWarnings.length > 0" class="mt-2 space-y-1">
            <div
                v-for="(warning, index) in validationWarnings"
                :key="index"
                class="flex items-start p-2 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-800"
            >
                <svg class="h-5 w-5 text-yellow-400 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span>{{ warning }}</span>
            </div>
        </div>
        
        <!-- Error message -->
        <p v-if="errorMessage" class="mt-1 text-sm text-red-600">{{ errorMessage }}</p>
    </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
    modelValue: {
        type: [Number, null],
        default: null
    },
    label: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: 'Tìm mã ICD-10...'
    },
    required: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    },
    errorMessage: {
        type: String,
        default: ''
    },
    patientId: {
        type: [Number, null],
        default: null
    }
});

const emit = defineEmits(['update:modelValue', 'selected', 'cleared']);

const input = ref(null);
const searchQuery = ref('');
const results = ref([]);
const loading = ref(false);
const showDropdown = ref(false);
const highlightedIndex = ref(0);
const selectedCode = ref(null);
const validationWarnings = ref([]);

const hasError = computed(() => !!props.errorMessage);

const filteredResults = computed(() => results.value);

// Debounced search
const performSearch = debounce(async () => {
    if (searchQuery.value.length < 2) {
        results.value = [];
        return;
    }
    
    loading.value = true;
    
    try {
        const response = await axios.get(route('icd10.search'), {
            params: {
                q: searchQuery.value,
                limit: 20
            }
        });
        
        results.value = response.data;
        highlightedIndex.value = 0;
    } catch (error) {
        console.error('Error searching ICD-10:', error);
        results.value = [];
    } finally {
        loading.value = false;
    }
}, 300);

const onInput = () => {
    if (selectedCode.value) {
        clearSelection();
    }
    performSearch();
};

const onBlur = () => {
    setTimeout(() => {
        showDropdown.value = false;
    }, 200);
};

const navigateDown = () => {
    if (highlightedIndex.value < filteredResults.value.length - 1) {
        highlightedIndex.value++;
    }
};

const navigateUp = () => {
    if (highlightedIndex.value > 0) {
        highlightedIndex.value--;
    }
};

const selectHighlighted = () => {
    if (filteredResults.value.length > 0 && highlightedIndex.value >= 0) {
        selectResult(filteredResults.value[highlightedIndex.value]);
    }
};

const selectResult = async (result) => {
    selectedCode.value = result;
    searchQuery.value = '';
    results.value = [];
    showDropdown.value = false;
    
    emit('update:modelValue', result.id);
    emit('selected', result);
    
    // Validate if patient is selected
    if (props.patientId) {
        await validateForPatient(result.id);
    }
};

const clearSelection = () => {
    selectedCode.value = null;
    searchQuery.value = '';
    validationWarnings.value = [];
    emit('update:modelValue', null);
    emit('cleared');
};

const closeDropdown = () => {
    showDropdown.value = false;
    if (!selectedCode.value) {
        searchQuery.value = '';
    }
};

const validateForPatient = async (icd10CodeId) => {
    if (!props.patientId) {
        validationWarnings.value = [];
        return;
    }
    
    try {
        const response = await axios.post(route('icd10.validate'), {
            icd10_code_id: icd10CodeId,
            patient_id: props.patientId
        });
        
        validationWarnings.value = response.data.warnings || [];
    } catch (error) {
        console.error('Error validating ICD-10:', error);
        validationWarnings.value = [];
    }
};

// Watch for modelValue changes (for initial value)
watch(() => props.modelValue, async (newValue) => {
    if (newValue && !selectedCode.value) {
        try {
            const response = await axios.get(route('icd10.show', newValue));
            selectedCode.value = response.data;
            
            if (props.patientId) {
                await validateForPatient(newValue);
            }
        } catch (error) {
            console.error('Error loading ICD-10:', error);
        }
    } else if (!newValue) {
        selectedCode.value = null;
        validationWarnings.value = [];
    }
}, { immediate: true });

// Watch for patient changes
watch(() => props.patientId, () => {
    if (selectedCode.value && props.patientId) {
        validateForPatient(selectedCode.value.id);
    }
});
</script>
