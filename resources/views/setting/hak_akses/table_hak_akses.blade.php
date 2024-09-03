<hr class="mb-3">

<div id="faq-accordion-2" class="accordion accordion-boxed">
    @foreach ($groupMenu->sortBy('sequence') as $i => $item)
        <div class="accordion-item">
            <div id="faq-accordion-content-{{ $i + 1 }}" class="accordion-header">
                <button class="accordion-button" type="button" data-tw-toggle="collapse"
                    data-tw-target="#faq-accordion-collapse-{{ $i + 1 }}" aria-expanded="true"
                    aria-controls="faq-accordion-collapse-{{ $i + 1 }}">
                   {{$item->name}}
                </button>
            </div>
            <div id="faq-accordion-collapse-{{ $i + 1 }}" class="accordion-collapse collapse"
                aria-labelledby="faq-accordion-content-{{ $i + 1 }}" data-tw-parent="#faq-accordion-2">
                <div class="accordion-body text-slate-600 dark:text-slate-500 leading-relaxed">
                    <table class="table table-striped">
                        <thead>
                            <th class="text-center">No</th>
                            <th>Nama</th>
                            <th class="text-center ">
                                <div class="icheck-primary">
                                    <label for="view">View</label>
                                    <input type="checkbox" id="view"
                                        onclick="gantiHakAksesGlobal('view',this,{{ $item->id }},{{ $req->id }})">
                                </div>
                            </th>
                            <th class="text-center">
                                <div class="icheck-primary">
                                    <label for="view">Create</label>
                                    <input type="checkbox" id="view"
                                        onclick="gantiHakAksesGlobal('create',this,{{ $item->id }},{{ $req->id }})">
                                </div>
                            </th>
                            <th class="text-center">
                                <div class="icheck-primary">
                                    <label for="view">Edit</label>
                                    <input type="checkbox" id="view"
                                        onclick="gantiHakAksesGlobal('edit',this,{{ $item->id }},{{ $req->id }})">
                                </div>
                            </th>
                            <th class="text-center">
                                <div class="icheck-primary">
                                    <label for="view">Delete</label>
                                    <input type="checkbox" id="view"
                                        onclick="gantiHakAksesGlobal('delete',this,{{ $item->id }},{{ $req->id }})">
                                </div>
                            </th>
                            <th class="text-center">
                                <div class="icheck-primary">
                                    <label for="view">Print</label>
                                    <input type="checkbox" id="view"
                                        onclick="gantiHakAksesGlobal('print',this,{{ $item->id }},{{ $req->id }})">
                                </div>
                            </th>
                            <th class="text-center">
                                <div class="icheck-primary">
                                    <label for="view">Validation</label>
                                    <input type="checkbox" id="view"
                                        onclick="gantiHakAksesGlobal('validation',this,{{ $item->id }},{{ $req->id }})">
                                </div>
                            </th>
                            <th class="text-center">
                                <div class="icheck-primary">
                                    <label for="view">Global</label>
                                    <input type="checkbox" id="view"
                                        onclick="gantiHakAksesGlobal('global',this,{{ $item->id }},{{ $req->id }})">
                                </div>
                            </th>
                        </thead>
                        @php
                            $index = 0;
                        @endphp
                        @foreach ($item->Menu->sortBy('sequence') as $i1 => $d1)
                            @php
                                $check = $d1->HakAkses->where('role_id', $req->id)->first();
                                if (is_null($check)) {
                                    $id = 0;
                                } else {
                                    $id = $check->id;
                                }
                                $index++;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $index }}</td>
                                <td>{{ $d1->name }}</td>
                                <td class="text-center">
                                    <div class="icheck-primary d-inline">
                                        <input type="checkbox" @if (!empty($check) and $check->view == 'true') checked @endif
                                            id="view_{{ $item->id }}_{{ $index }}"
                                            onclick="gantiHakAkses({{ $id }},'view',this,{{ $req->id }},{{ $d1->id }})"
                                            class="view_{{ $item->id }}">
                                        <label for="view_{{ $item->id }}_{{ $index }}">
                                        </label>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="icheck-primary d-inline">
                                        <input type="checkbox" @if (!empty($check) and $check->create == 'true') checked @endif
                                            id="create_{{ $item->id }}_{{ $index }}"
                                            onclick="gantiHakAkses({{ $id }},'create',this,{{ $req->id }},{{ $d1->id }})"
                                            class="create_{{ $item->id }}">
                                        <label for="create_{{ $item->id }}_{{ $index }}">
                                        </label>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="icheck-primary d-inline">
                                        <input type="checkbox" @if (!empty($check) and $check->edit == 'true') checked @endif
                                            id="edit_{{ $item->id }}_{{ $index }}"
                                            onclick="gantiHakAkses({{ $id }},'edit',this,{{ $req->id }},{{ $d1->id }})"
                                            class="edit_{{ $item->id }}">
                                        <label for="edit_{{ $item->id }}_{{ $index }}">
                                        </label>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="icheck-primary d-inline">
                                        <input type="checkbox" @if (!empty($check) and $check->delete == 'true') checked @endif
                                            id="delete_{{ $item->id }}_{{ $index }}"
                                            onclick="gantiHakAkses({{ $id }},'delete',this,{{ $req->id }},{{ $d1->id }})"
                                            class="delete_{{ $item->id }}">
                                        <label for="delete_{{ $item->id }}_{{ $index }}">
                                        </label>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="icheck-primary d-inline">
                                        <input type="checkbox" @if (!empty($check) and $check->print == 'true') checked @endif
                                            id="print_{{ $item->id }}_{{ $index }}"
                                            onclick="gantiHakAkses({{ $id }},'print',this,{{ $req->id }},{{ $d1->id }})"
                                            class="print_{{ $item->id }}">
                                        <label for="print_{{ $item->id }}_{{ $index }}">
                                        </label>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="icheck-primary d-inline">
                                        <input type="checkbox" @if (!empty($check) and $check->validation == 'true') checked @endif
                                            id="validation_{{ $item->id }}_{{ $index }}"
                                            onclick="gantiHakAkses({{ $id }},'validation',this,{{ $req->id }},{{ $d1->id }})"
                                            class="validation_{{ $item->id }}">
                                        <label for="validation_{{ $item->id }}_{{ $index }}">
                                        </label>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="icheck-primary d-inline">
                                        <input type="checkbox" @if (!empty($check) and $check->global == 'true') checked @endif
                                            id="global_{{ $item->id }}_{{ $index }}"
                                            onclick="gantiHakAkses({{ $id }},'global',this,{{ $req->id }},{{ $d1->id }})"
                                            class="global_{{ $item->id }}">
                                        <label for="global_{{ $item->id }}_{{ $index }}">
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    @endforeach
</div>
