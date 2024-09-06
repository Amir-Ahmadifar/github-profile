function fetchGithubProfile(username) {
    jQuery.ajax({
        url: githubProfileData.ajaxurl + '?action=fetch_github_profile&username=' + username,
        method: 'GET',
        success: function (response) {
            if (response.success) {
                const profile = response.data;
                jQuery('.github-content').html(`
                    <img class="github-avatar" src="${profile.avatar_url}" alt="${profile.login}" />
                    <h3 class="github-name">${profile.name || profile.login}</h3>
                    <p class="github-bio">${profile.bio || 'No bio available'}</p>
                    <p class="github-stats">Followers: ${profile.followers} | Following: ${profile.following}</p>
                    <p class="github-stats">Public Repos: ${profile.public_repos}</p>
                    <a class="github-link" href="${profile.html_url}" target="_blank">View Profile on Github</a>
                `);
            } else {
                jQuery('.github-content').html('<p>خطا در دریافت اطلاعات</p>');
            }
        },
        error: function () {
            jQuery('.github-content').html('<p>خطایی رخ داده است</p>');
        }
    });
}
