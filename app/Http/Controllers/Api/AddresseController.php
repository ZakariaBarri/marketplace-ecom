<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Addresse;
use Illuminate\Http\Request;

use App\Traits\ApiResponse;

class AddresseController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Addresse::class);

        $addresses = auth()->user()
            ->addresses()
            ->latest()
            ->paginate(10);

        return $this->success(AddressResource::collection($addresses));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAddressRequest $request)
    {
        $this->authorize('create', Addresse::class);

        $user = auth()->user();

        $address = $user->addresses()->create($request->validated());

        return $this->success(new AddressResource($address), 'Address created', 201);
    }

    public function show(Addresse $address)
    {
        $this->authorize('view', $address);

        return $this->success(new AddressResource($address));
    }

    public function update(UpdateAddressRequest $request, Addresse $address)
    {
        $this->authorize('update', $address);

        if ($address->orders()->exists()) {
            return $this->error('Address is used in orders');
        }

        $address->update($request->validated());

        return $this->success(new AddressResource($address), 'Address updated');
    }

    public function destroy(Addresse $address)
    {
        $this->authorize('delete', $address);

        if ($address->orders()->exists()) {
            return $this->error('Address is used in orders');
        }

        $address->delete();

        return $this->success(null, 'Deleted');
    }
}
