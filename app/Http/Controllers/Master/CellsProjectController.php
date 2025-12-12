<?php

namespace App\Http\Controllers\Master;

use App\Helpers\Response;
use App\Models\CellsProject;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CellsProjectRequest;

class CellsProjectController extends Controller
{
    public function store(CellsProjectRequest $request)
    {
        try {
            $data = $request->validated();
            $projects = $data['projects'] ?? [];
            unset($data['projects']);
            
            $cellsProject = CellsProject::create($data);
            
            // Create projects
            if (!empty($projects)) {
                foreach ($projects as $projectName) {
                    if (!empty($projectName)) {
                        \App\Models\Project::create([
                            'id_cells_project' => $cellsProject->id_cells_project,
                            'nama_project' => $projectName
                        ]);
                    }
                }
            }
            
            return Response::success(null, 'Cells Project berhasil ditambahkan');
        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
    }

    public function edit($id)
    {
        $project = CellsProject::with('projects')->where('id_cells_project', $id)->firstOrFail();
        return Response::success($project, 'Cells Project berhasil diambil');
    }

    public function update(CellsProjectRequest $request, $id)
    {
        try {
            $cellsProject = CellsProject::where('id_cells_project', $id)->firstOrFail();
            $data = $request->validated();
            $projects = $data['projects'] ?? [];
            unset($data['projects']);
            
            $cellsProject->update($data);
            
            // Sync projects - delete old and create new
            $cellsProject->projects()->delete();
            
            if (!empty($projects)) {
                foreach ($projects as $projectName) {
                    if (!empty($projectName)) {
                        \App\Models\Project::create([
                            'id_cells_project' => $cellsProject->id_cells_project,
                            'nama_project' => $projectName
                        ]);
                    }
                }
            }
            
            return Response::success(null, 'Cells Project berhasil diupdate');
        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
    }

    public function destroy($id)
    {
        try {
            $project = CellsProject::where('id_cells_project', $id)->firstOrFail();
            $project->delete();
            return Response::success(null, 'Cells Project berhasil dihapus');
        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
    }
}
