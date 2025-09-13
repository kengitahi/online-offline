<div
    class="p-8 max-w-lg mx-auto bg-gray-100 rounded-xl shadow-lg my-12"
    x-data="toastHandler()"
    wire:ignore.self
    x-init="
        setTimeout(() => { showStatusMessage = false }, 3000);

        const showTemporaryMessage = (message) => {
            messageContent = message;
            showMessage = true;
            
            // clear any old timers first
            if (window._toastTimer) {
                clearTimeout(window._toastTimer);
            }

            window._toastTimer = setTimeout(() => {
                showMessage = false;
            }, 3000);
        };
        
        // Initial status check
        $wire.set('isOnline', isOnline);

        //Set initial status
        $wire.updateStatus();

        // Update Alpine.js `statusMessage`
        $watch('statusMessage', (newVal) => {
            if (newVal) {
                const showNow = () => {
                    showStatusMessage = true;

                    if (window._statusTimer) clearTimeout(window._statusTimer);
                    window._statusTimer = setTimeout(() => {
                        showStatusMessage = false;
                    }, 3000);
                };
                
                //If another toast is showing
                if (showMessage) {
                    // wait until the toast disappears
                    setTimeout(showNow, 3000);
                } else {
                    // show immediately
                    showNow();
                }
            }
        });

        // Event listener for when the app goes offline.
        window.addEventListener('offline', () => {
            isOnline = false;
            showTemporaryMessage('You are now offline.');
            $wire.set('isOnline', false);
            $wire.updateStatus();
        });

        // Event listener for when the app comes online.
        window.addEventListener('online', () => {
            isOnline = true;
            showTemporaryMessage('You are back online.');
            $wire.set('isOnline', true);
            $wire.updateStatus();
            $wire.syncData();
        });
     "
>

    <h2 class="text-3xl font-bold text-center mb-6 text-gray-800">Create a New Post</h2>

    <!-- Toast to show status -->
    <div
        class="mb-4 p-4 rounded-md shadow"
        :class="isOnline ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
        x-show="showMessage"
        x-transition.duration.500ms
        x-cloak
    >
        <span
            class="font-semibold"
            x-text="messageContent"
        ></span>
    </div>

    <!-- Toasts to show connection status message -->
    <div
        class="mb-6 p-4 rounded-md shadow-sm transition-colors duration-300"
        :class="isOnline ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
        x-show="showStatusMessage"
        x-transition.duration.500ms
        x-cloak
    >
        <span
            class="font-semibold text-center block"
            x-text="statusMessage"
        ></span>
    </div>

    <!-- The form for creating a new post. -->
    <form
        wire:submit.prevent="savePost"
        x-data="{isOnline: window.navigator.onLine}"
        class="bg-white p-6 rounded-lg shadow-lg"
    >
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="mb-4">
            <label
                for="title"
                class="block text-gray-700 font-semibold mb-2"
            >Post Title</label>
            <input
                type="text"
                id="title"
                wire:model.live="title"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition-shadow"
            >
            @error('title') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label
                for="content"
                class="block text-gray-700 font-semibold mb-2"
            >Post Content</label>
            <textarea
                id="content"
                wire:model.live="content"
                rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition-shadow"
            ></textarea>
            @error('content') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0 sm:space-x-4">
            <button
                type="submit"
                class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-md transition-colors duration-200 shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                Save Post
            </button>

            <button
                type="button"
                wire:click="syncData"
                x-show="isOnline"
                class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-md transition-colors duration-200 shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
            >
                <div
                    wire:loading
                    wire:target="syncData"
                    class="flex items-center justify-center"
                >
                    <svg
                        class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                    </svg>
                    Syncing...
                </div>
                <span
                    wire:loading.remove
                    wire:target="syncData"
                >Sync Data</span>
            </button>
        </div>
    </form>
</div>

<script>
    function toastHandler() {
        return {
            isOnline: navigator.onLine,
            showMessage: false,
            showStatusMessage: true,
            messageContent: '',
            statusMessage: @entangle('statusMessage'),
            showTemporaryMessage(message) {
                this.messageContent = message
                this.showMessage = true
                if (this._timer) clearTimeout(this._timer)
                this._timer = setTimeout(() => { this.showMessage = false }, 3000)
            }
        }
    }
</script>