<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100">Edit user</h2>
      <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 underline hover:text-gray-900 dark:text-gray-300">Back</a>
    </div>
  </x-slot>

  <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <form method="POST" action="{{ route('admin.users.update',$user) }}" class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        @csrf @method('PUT')

        <div class="sm:col-span-2">
          <x-input-label value="Full name" />
          <x-text-input type="text" name="full_name" class="mt-1 block w-full" required value="{{ old('full_name',$user->full_name) }}" />
          <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
        </div>

        <div>
          <x-input-label value="Email" />
          <x-text-input type="email" name="email" class="mt-1 block w-full" required value="{{ old('email',$user->email) }}" />
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
          <x-input-label value="Phone" />
          <x-text-input type="text" name="phone" class="mt-1 block w-full" value="{{ old('phone',$user->phone) }}" />
          <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div>
          <x-input-label value="Role" />
          <select name="role" required
                  class="mt-1 block w-full rounded-md border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            @foreach($roles as $r)
              <option value="{{ $r->value }}" @selected(old('role',$user->role->value)===$r->value)>{{ ucfirst($r->value) }}</option>
            @endforeach
          </select>
          <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div>
          <x-input-label value="New password (optional)" />
          <x-text-input type="password" name="password" class="mt-1 block w-full" />
          <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
          <x-input-label value="Confirm password" />
          <x-text-input type="password" name="password_confirmation" class="mt-1 block w-full" />
        </div>

        <div class="sm:col-span-2 flex items-center justify-end gap-3 pt-2">
          <a href="{{ route('admin.users.index') }}"
             class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
            Cancel
          </a>
          <x-primary-button>Update</x-primary-button>
        </div>
      </form>
    </div>
  </div>
</x-app-layout>
