<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    use HasFactory;

    protected $table = 'series';

    protected $fillable = [
        'organ',
        'code',
        'name',
        'course',
        'level',
        'status'
    ];

    public function rules(): array
    {
        return [
            'organ'   => 'required',
            'code'    => ['required', Rule::unique('series')->ignore($this->id)],
            'name'    => 'required',
            'course'  => 'required',
            'level'   => 'required',
            'status'  => 'required' 
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Campo obrigatório não informado...',
            'unique' => 'Série já registrado no sistema...'
        ];
    }

    public function organ():HasOne
    {
        return $this->hasOne(Organ::class,'id','organ');
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function grid():BelongsTo
    {
        return $this->belongsTo(Grid::class);
    }

    public static function list_courses():array{
        return [
            ['id' => 1, 'title' => '']
        ];
    }

    public static function list_levels():array{
        return [
            ['id' => 1, 'title' => '']
        ];
    }

    public static function list_status():array{
        return [
            ['id' => 1, 'title' => '']
        ];
    }
}
