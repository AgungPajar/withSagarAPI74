<?php

namespace App\Http\Resources;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => Hashids::encode($this->id), // Hash ID di sini
            'name' => $this->name,
            'nisn' => $this->nisn,
            'class' => $this->class,
            'jurusan' => $this->whenLoaded('jurusan', $this->jurusan->nama ?? null),
            'phone' => $this->phone,
            'tanggal_lahir' => $this->tanggal_lahir,
            'alamat' => $this->alamat,
            'clubs' => $this->whenLoaded('clubs'),
        ];
    }
}
