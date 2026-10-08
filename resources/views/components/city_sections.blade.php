@forelse((dv('request.home_sections',get_defined_vars())??[]) as $section)@include(dv('section.template_name',get_defined_vars()))@empty @endforelse
