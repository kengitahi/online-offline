<section>
    <div
        x-data="{ isOnline: window.navigator.onLine, showMessage: false, messageContent: '' }"
        x-init="
        const showTemporaryMessage = (message) => {
            messageContent = message;
            showMessage = true;
            setTimeout(() => {
                showMessage = false;
            }, 3000); // Message disappears after 3 seconds
        };

        // Initial message on page load
        if (isOnline) {
            showTemporaryMessage('Status: Online.');
        } else {
            showTemporaryMessage('Status: Offline.');
        }

        // Event listener for when the app goes offline
        window.addEventListener('offline', () => {
            isOnline = false;
            showTemporaryMessage('You are now offline.');
            $wire.updateStatus();
        });

        // Event listener for when the app comes online
        window.addEventListener('online', () => {
            isOnline = true;
            showTemporaryMessage('You are back online.');
            $wire.updateStatus();
            $wire.syncData();
        });
     "
    >

        <div
            class="mb-4 p-4 rounded-md shadow"
            :class="isOnline ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
            x-show.transition.duration.500ms="showMessage"
            x-cloak
            x-transition
        >
            <span
                class="font-semibold"
                x-text="messageContent"
            ></span>
        </div>
        <!-- Rest of your form content -->
        <h1>{{ $count }}</h1>

        <button wire:click="increment">+</button>

        <button wire:click="decrement">-</button>
    </div>
</section>