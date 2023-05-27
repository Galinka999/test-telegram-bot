<i>ФИО - </i> {{ $user->name }}
<i>Должность - </i> {{ $user->position }}
<i>Телефон - </i> {{ $user->phone }}
<i>Дата рождения - </i> {{ $user->birthday->format('d.m.Y') }}
<i>Дата увольнения - </i> {{ $user->deleted_at->format('d.m.Y') }}

