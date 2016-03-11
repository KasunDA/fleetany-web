<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ContactRepository;
use App\Entities\Contact;

class ContactRepositoryEloquent extends BaseRepository implements ContactRepository
{

    protected $rules = [
        'company_id'      => 'required',
        'contact_type_id'   => 'required',
        'name'      => 'min:3|required',
        'license_no'  => 'required',
        ];

    public function model()
    {
        return Contact::class;
    }

    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
    public function results($filters = array())
    {
        $contacts = $this->scopeQuery(function ($query) use ($filters) {
            
            if (!empty($filters['name'])) {
                $query = $query->where('contacts.name', 'like', '%'.$filters['name'].'%');
            }
            if (!empty($filters['contact-type'])) {
                $query = $query->join('types', 'contacts.contact_type_id', '=', 'types.id');
                $query = $query->where('types.name', 'like', '%'.$filters['contact-type'].'%');
            }
            if (!empty($filters['city'])) {
                $query = $query->where('contacts.city', 'like', '%'.$filters['city'].'%');
            }

            $query = $query->orderBy('contacts.'.$filters['sort'], $filters['order']);
            
            return $query;
        })->paginate($filters['paginate']);
        
        return $contacts;
    }
}