{{-- The page builder: each section (a set of the `blocks` Replicator, in the
     control panel's order) renders components/blocks/{type}. A section
     switched off in the control panel never reaches here; a type with no
     view is skipped, and so is one the blueprint no longer has (Statamic
     passes it on as a plain array). The challenge's scroll story binds to
     one section only, so a second copy renders nothing. --}}
@props(['blocks' => [], 'page' => null])
@php($rendered = [])
@foreach (\Statamic\View\Blade\value($blocks) ?? [] as $block)
    @php($view = $block instanceof \Statamic\Fields\Values ? 'components.blocks.'.str_replace('_', '-', (string) $block->type) : null)
    @continue(! $view || ! view()->exists($view))
    @continue($block->type === 'challenge' && in_array('challenge', $rendered, true))
    @php($rendered[] = (string) $block->type)
    @include($view, ['block' => $block, 'page' => $page])
@endforeach
