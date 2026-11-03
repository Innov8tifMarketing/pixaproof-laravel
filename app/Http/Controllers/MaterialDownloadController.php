<?php

namespace App\Http\Controllers;

use App\Support\DownloadableMaterials;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Hands out a downloadable material's file: to the session that asked for it on the site, or to
 * anyone holding the signed link from the confirmation email. Anyone else goes to the materials.
 */
class MaterialDownloadController extends Controller
{
    public function __invoke(Request $request, string $material): StreamedResponse|RedirectResponse
    {
        $entry = DownloadableMaterials::findBySlug($material);
        $file = $entry ? DownloadableMaterials::file($entry) : null;

        abort_if($entry === null || $file === null, 404);

        if (! $request->hasValidSignature() && ! DownloadableMaterials::isGranted($entry)) {
            return redirect('/#resources');
        }

        return Storage::disk(DownloadableMaterials::DISK)
            ->download($file->path(), "{$entry->slug()}.{$file->extension()}");
    }
}
