@if (user_enrolled($course))
    <div id="notes" class="inside">
        <div class="toggle"><i class="close icon"></i></div>

        <h4><i class="pencil icon"></i> Notes</h4>
        <textarea v-model="notes" placeholder="Just start typing, it's automatically saved">
                                    </textarea>

        <div id="print-notes">
            <a href="{{ route('notes', $course->slug) }}">
                <i class="text file icon"></i>
                View All Notes
            </a>
        </div>
    </div>
@endif