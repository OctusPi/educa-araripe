<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $table = 'subjects';

    protected $fillable = ['organ', 'name', 'area', 'description'];

    public function rules(): array
    {
        return [
            'organ'   => 'required',
            'name'  => 'required',
            'area'   => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Campo obrigatório não informado...'
        ];
    }

    public function organ():HasOne
    {
        return $this->hasOne(Organ::class,'id','organ');
    }

    public function grid():BelongsTo
    {
        return $this->belongsTo(Grid::class);
    }

    public static function list_areas(): array
    {
        return [
            ['id' => 1, 'title' => 'Ciências Humanas e suas Tecnologias'],
            ['id' => 2, 'title' => 'Ciências da Natureza e suas Tecnologias'],
            ['id' => 3, 'title' => 'Linguagens, Códigos e suas Tecnologias'],
            ['id' => 4, 'title' => 'Matemática e suas Tecnologias'],
        ];
    }
}
